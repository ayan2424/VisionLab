<?php

namespace App\Services;

use App\Models\AiActionsLog;
use App\Models\AiChatSession;
use App\Models\AiMessage;
use App\Models\AiPendingPatch;
use App\Models\Workspace;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

/**
 * AiService — Sovereign AI Agent orchestrator.
 *
 * Proxies LLM requests through the platform, enforces the HITL (Human-In-The-Loop)
 * patch approval protocol, and logs all AI activity for forensic auditing.
 *
 * Zero direct write access: All mutations transit through ai_pending_patches.
 */
class AiService
{
    private AiSandbox $sandbox;

    public function __construct(AiSandbox $sandbox)
    {
        $this->sandbox = $sandbox;
    }

    /**
     * Handle an OpenAI-compatible chat completion request and proxy to Anthropic.
     * Returns a generator that yields SSE strings.
     */
    public function handleChatCompletion(Workspace $workspace, array $messages, string $mode = 'CHAT', bool $stream = true, ?int $userId = null)
    {
        $apiKey = config('visionlab.ai.api_key') ?: env('ANTHROPIC_API_KEY');

        // Log the AI action
        $this->logAction($workspace, $userId, 'chat_completion', [
            'mode'          => $mode,
            'message_count' => count($messages),
        ]);

        // Check daily rate limit
        $todayCount = AiActionsLog::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= config('visionlab.ai.daily_limit', 100)) {
            yield "data: " . json_encode(['choices' => [['delta' => ['content' => "⚠️ Daily AI usage limit reached. Please try again tomorrow."]]]]) . "\n\n";
            yield "data: [DONE]\n\n";
            return;
        }

        if (empty($apiKey)) {
            // Demo mode — simulate response for development
            yield "data: " . json_encode(['choices' => [['delta' => ['content' => "I'm in demo mode! I've analyzed your workspace. I'll propose a patch now."]]]]) . "\n\n";
            yield "data: " . json_encode(['choices' => [['delta' => ['content' => "\n\n*[Using tool: propose_patch...]*\n"]]]]) . "\n\n";

            // Emit demo patch via pending patches table
            $patch = AiPendingPatch::create([
                'workspace_id'     => $workspace->id,
                'session_id'       => null,
                'file_path'        => 'demo.php',
                'diff'             => "+ echo 'Hello from VisionLab AI!';\n- echo 'old code';",
                'original_content' => "echo 'old code';",
                'patched_content'  => "echo 'Hello from VisionLab AI!';",
                'status'           => 'pending',
                'created_by'       => $userId ?? auth()->id() ?? 1,
            ]);

            event(new \App\Events\PatchProposed('ws-' . $workspace->id, [
                'patch_id'         => $patch->id,
                'file_path'        => $patch->file_path,
                'diff'             => $patch->diff,
                'original_content' => $patch->original_content,
                'patched_content'  => $patch->patched_content,
            ]));

            yield "data: [DONE]\n\n";
            return;
        }

        // Build Anthropic messages
        $anthropicMessages = [];
        $systemPrompt = $this->getSystemPrompt($mode);

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemPrompt .= "\n" . $msg['content'];
                continue;
            }
            $anthropicMessages[] = [
                'role'    => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['content'],
            ];
        }

        // Define tools the AI can use
        $tools = $this->getToolDefinitions();

        // Make the API call
        $model = config('visionlab.ai.model', 'claude-3-5-sonnet-20241022');
        $maxTokens = config('visionlab.ai.max_tokens', 4096);

        $client = new Client();
        $requestData = [
            'model'      => $model,
            'max_tokens' => $maxTokens,
            'system'     => $systemPrompt,
            'messages'   => $anthropicMessages,
            'tools'      => $tools,
            'stream'     => $stream,
        ];

        try {
            $response = $client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key'         => $apiKey,
                    'anthropic-version'  => '2023-06-01',
                    'content-type'       => 'application/json',
                ],
                'json'   => $requestData,
                'stream' => $stream,
            ]);

            if (!$stream) {
                $body = json_decode($response->getBody()->getContents(), true);
                yield $this->handleAnthropicResponse($workspace, $body, $userId);
                return;
            }

            // Stream SSE response, translating Anthropic → OpenAI format
            $body = $response->getBody();
            $buffer = '';

            while (!$body->eof()) {
                $chunk = $body->read(1024);
                $buffer .= $chunk;

                while (($pos = strpos($buffer, "\n\n")) !== false) {
                    $eventStr = substr($buffer, 0, $pos);
                    $buffer = substr($buffer, $pos + 2);

                    if (str_starts_with($eventStr, 'event: ')) {
                        $eventLines = explode("\n", $eventStr);
                        $eventName = substr($eventLines[0], 7);
                        $dataLine = $eventLines[1] ?? '';

                        if (str_starts_with($dataLine, 'data: ')) {
                            $data = json_decode(substr($dataLine, 6), true);

                            // Map Anthropic content_block_delta to OpenAI format
                            if ($eventName === 'content_block_delta' && isset($data['delta']['text'])) {
                                $openaiChunk = [
                                    'id'      => 'chatcmpl-vl-' . uniqid(),
                                    'object'  => 'chat.completion.chunk',
                                    'created' => time(),
                                    'model'   => $model,
                                    'choices' => [[
                                        'index'         => 0,
                                        'delta'         => ['content' => $data['delta']['text']],
                                        'finish_reason' => null,
                                    ]],
                                ];
                                yield "data: " . json_encode($openaiChunk) . "\n\n";
                            }

                            // Handle tool call blocks
                            if ($eventName === 'content_block_start' && isset($data['content_block']['type']) && $data['content_block']['type'] === 'tool_use') {
                                $toolName = $data['content_block']['name'];
                                yield "data: " . json_encode(['choices' => [['delta' => ['content' => "\n\n*[Running {$toolName}...]*\n"]]]]) . "\n\n";

                                if ($toolName === 'propose_patch') {
                                    $patch = AiPendingPatch::create([
                                        'workspace_id'     => $workspace->id,
                                        'file_path'        => 'pending',
                                        'diff'             => '+ // AI Proposed Change',
                                        'original_content' => '',
                                        'patched_content'  => '// AI Proposed Change',
                                        'status'           => 'pending',
                                    ]);

                                    event(new \App\Events\PatchProposed('ws-' . $workspace->id, [
                                        'patch_id'         => $patch->id,
                                        'file_path'        => $patch->file_path,
                                        'diff'             => $patch->diff,
                                        'original_content' => $patch->original_content,
                                        'patched_content'  => $patch->patched_content,
                                    ]));
                                }
                            }
                        }
                    }
                }
            }

            yield "data: [DONE]\n\n";

        } catch (\Exception $e) {
            Log::error("AiService error: " . $e->getMessage());
            yield "data: " . json_encode(['choices' => [['delta' => ['content' => "API Error: " . $e->getMessage()]]]]) . "\n\n";
            yield "data: [DONE]\n\n";
        }
    }

    /**
     * Execute an agreed-upon plan autonomously.
     */
    public function executePlan(Workspace $workspace, $user)
    {
        $this->logAction($workspace, $user->id, 'execute_plan', []);

        $systemPrompt = $this->getSystemPrompt('AGENT');

        $messages = [
            ['role' => 'user', 'content' => "Please implement the plan we just agreed upon. Propose the necessary patches using the `propose_patch` tool."],
        ];

        foreach ($this->handleChatCompletion($workspace, $messages, 'AGENT', false, $user->id) as $chunk) {
            // Process tool call results
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // Tool Definitions
    // ══════════════════════════════════════════════════════════════════════

    private function getToolDefinitions(): array
    {
        return [
            [
                'name'         => 'read_file',
                'description'  => 'Read the contents of a file in the workspace.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'path' => ['type' => 'string', 'description' => 'Relative path to the file'],
                    ],
                    'required' => ['path'],
                ],
            ],
            [
                'name'         => 'list_directory',
                'description'  => 'List contents of a directory in the workspace.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'path' => ['type' => 'string', 'description' => 'Relative path to directory'],
                    ],
                    'required' => ['path'],
                ],
            ],
            [
                'name'         => 'search_codebase',
                'description'  => 'Search the codebase using a text query.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Text or regex to search'],
                    ],
                    'required' => ['query'],
                ],
            ],
            [
                'name'         => 'propose_patch',
                'description'  => 'Propose a code modification. Creates a pending patch for human approval (HITL protocol).',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'file_path'     => ['type' => 'string', 'description' => 'Relative path to file'],
                        'search_block'  => ['type' => 'string', 'description' => 'The exact original text to replace'],
                        'replace_block' => ['type' => 'string', 'description' => 'The new text to insert'],
                    ],
                    'required' => ['file_path', 'search_block', 'replace_block'],
                ],
            ],
            [
                'name'         => 'run_terminal',
                'description'  => 'Execute a terminal command in the workspace sandbox. Output is returned.',
                'input_schema' => [
                    'type'       => 'object',
                    'properties' => [
                        'command' => ['type' => 'string', 'description' => 'The shell command to execute'],
                    ],
                    'required' => ['command'],
                ],
            ],
        ];
    }

    private function getSystemPrompt(string $mode): string
    {
        $base = "You are VisionLab, an elite coding assistant built into the IDE. You are part of the Aptech Vision 2026 platform.";

        return match ($mode) {
            'AGENT' => $base . " You are an autonomous coding agent. You can read, write, and apply patches using tools. All writes must be confirmed by the user via a patch preview (use propose_patch). Never write directly to files.",
            'PLAN'  => $base . " You are a strategic coding planner. Analyze, outline steps, mention files, but do not write code modifications. At the very end of your plan, output: \n\n[🚀 Start Implementation](command:visioncode.startImplementation)",
            default => $base . " You are a helpful coding assistant. You can read and search the codebase but never modify files.",
        };
    }

    private function handleAnthropicResponse(Workspace $workspace, array $body, ?int $userId)
    {
        // Handle non-streamed response — process tool calls
        if (isset($body['content'])) {
            foreach ($body['content'] as $block) {
                if ($block['type'] === 'tool_use' && $block['name'] === 'propose_patch') {
                    $input = $block['input'];
                    AiPendingPatch::create([
                        'workspace_id'     => $workspace->id,
                        'file_path'        => $input['file_path'] ?? 'unknown',
                        'diff'             => "- " . ($input['search_block'] ?? '') . "\n+ " . ($input['replace_block'] ?? ''),
                        'original_content' => $input['search_block'] ?? '',
                        'patched_content'  => $input['replace_block'] ?? '',
                        'status'           => 'pending',
                    ]);
                }
            }
        }
        return $body;
    }

    private function logAction(Workspace $workspace, ?int $userId, string $action, array $context): void
    {
        AiActionsLog::create([
            'user_id'      => $userId,
            'workspace_id' => $workspace->id,
            'action_type'  => $action,
            'context'      => $context,
            'ip_address'   => request()?->ip(),
        ]);
    }
}
