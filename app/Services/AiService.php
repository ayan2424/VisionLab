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
    public function handleChatCompletion(Workspace $workspace, array $messages, string $mode = 'CHAT', bool $stream = true, ?int $userId = null, string $requestedModel = 'claude-3-5-sonnet-20241022')
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

        // Exam Mode & AI allowed checks
        if ($workspace->assignment) {
            $assignment = $workspace->assignment;
            if ($assignment->mode === 'exam' || !$assignment->allow_ai) {
                yield "data: " . json_encode(['choices' => [['delta' => ['content' => "⚠️ AI assistance is disabled for this exam or assignment."]]]]) . "\n\n";
                yield "data: [DONE]\n\n";
                return;
            }
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
        $systemPrompt = $this->getSystemPrompt($mode);
        $anthropicMessages = [];

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
        $model = $requestedModel;
        $maxTokens = config('visionlab.ai.max_tokens', 4096);

        $client = new Client();
        $loopSafetyLimit = 10;
        $currentTurn = 0;

        while ($currentTurn < $loopSafetyLimit) {
            $currentTurn++;

            $requestData = [
                'model'      => $model,
                'max_tokens' => $maxTokens,
                'system'     => $systemPrompt,
                'messages'   => $anthropicMessages,
                'tools'      => $tools,
            ];

            try {
                $response = $client->post('https://api.anthropic.com/v1/messages', [
                    'headers' => [
                        'x-api-key'         => $apiKey,
                        'anthropic-version' => '2023-06-01',
                        'content-type'      => 'application/json',
                    ],
                    'json'   => $requestData,
                ]);

                $body = json_decode($response->getBody()->getContents(), true);
                $stopReason = $body['stop_reason'] ?? '';
                $content = $body['content'] ?? [];

                // Append assistant response to history
                $anthropicMessages[] = [
                    'role'    => 'assistant',
                    'content' => $content,
                ];

                // Yield text delta chunks of this turn
                foreach ($content as $block) {
                    if ($block['type'] === 'text') {
                        $openaiChunk = [
                            'id'      => 'chatcmpl-vl-' . uniqid(),
                            'object'  => 'chat.completion.chunk',
                            'created' => time(),
                            'model'   => $model,
                            'choices' => [[
                                'index'         => 0,
                                'delta'         => ['content' => $block['text']],
                                'finish_reason' => null,
                            ]],
                        ];
                        yield "data: " . json_encode($openaiChunk) . "\n\n";
                    }
                }

                // If stop reason is not tool_use, we have reached the end of the chain
                if ($stopReason !== 'tool_use') {
                    yield "data: [DONE]\n\n";
                    return;
                }

                // Run tools sequentially
                $toolResults = [];
                foreach ($content as $block) {
                    if ($block['type'] === 'tool_use') {
                        $toolName = $block['name'];
                        $toolId   = $block['id'];
                        $input    = $block['input'];

                        // Yield status updates to user
                        yield "data: " . json_encode([
                            'choices' => [[
                                'delta' => ['content' => "\n⚙️ *[Running tool: {$toolName}...]*\n"],
                                'finish_reason' => null
                            ]]
                        ]) . "\n\n";

                        $toolOutput = '';
                        try {
                            switch ($toolName) {
                                case 'read_file':
                                    $path = $input['path'] ?? '';
                                    $toolOutput = $this->sandbox->readFile($workspace, $path) ?? "Error: File not found or access denied: '{$path}'";
                                    break;
                                case 'list_directory':
                                    $path = $input['path'] ?? '';
                                    $items = $this->sandbox->listDirectory($workspace, $path);
                                    $toolOutput = json_encode($items, JSON_PRETTY_PRINT);
                                    break;
                                case 'search_codebase':
                                    $query = $input['query'] ?? '';
                                    $items = $this->sandbox->searchCodebase($workspace, $query);
                                    $toolOutput = json_encode($items, JSON_PRETTY_PRINT);
                                    break;
                                case 'run_terminal':
                                    $command = $input['command'] ?? '';
                                    $toolOutput = app(CodeServerManager::class)->runTerminalCommand($workspace, $command);
                                    break;
                                case 'propose_patch':
                                    $filePath = $input['file_path'] ?? '';
                                    $search = $input['search_block'] ?? '';
                                    $replace = $input['replace_block'] ?? '';
                                    $res = $this->sandbox->preparePatch($workspace, null, $filePath, $search, $replace);
                                    if (isset($res['error'])) {
                                        $toolOutput = "Error preparing patch: " . $res['error'];
                                    } else {
                                        $toolOutput = "Patch prepared successfully with ID " . $res['patch_id'] . ". User will review it via the diff viewer.";
                                        
                                        // Broadcast PatchProposed event for UI update
                                        event(new \App\Events\PatchProposed('ws-' . $workspace->id, [
                                            'patch_id'         => $res['patch_id'],
                                            'file_path'        => $filePath,
                                            'diff'             => $res['diff'],
                                            'original_content' => $search,
                                            'patched_content'  => $replace,
                                        ]));
                                    }
                                    break;
                                default:
                                    $toolOutput = "Error: Tool '{$toolName}' is not supported.";
                            }
                        } catch (\Throwable $err) {
                            $toolOutput = "Exception during tool execution: " . $err->getMessage();
                        }

                        $toolResults[] = [
                            'type'        => 'tool_result',
                            'tool_use_id' => $toolId,
                            'content'     => $toolOutput,
                        ];

                        yield "data: " . json_encode([
                            'choices' => [[
                                'delta' => ['content' => "✅ *[{$toolName} output captured]*\n\n"],
                                'finish_reason' => null
                            ]]
                        ]) . "\n\n";
                    }
                }

                // Add tool results as a user turn
                $anthropicMessages[] = [
                    'role'    => 'user',
                    'content' => $toolResults,
                ];

            } catch (\Exception $e) {
                Log::error("AiService error: " . $e->getMessage());
                yield "data: " . json_encode(['choices' => [['delta' => ['content' => "API Error: " . $e->getMessage()]]]]) . "\n\n";
                yield "data: [DONE]\n\n";
                return;
            }
        }

        yield "data: " . json_encode(['choices' => [['delta' => ['content' => "⚠️ Safety recursion limit reached."]]]]) . "\n\n";
        yield "data: [DONE]\n\n";
    }

    /**
     * Get a direct single-turn text completion from Anthropic's Claude API.
     */
    public function getDirectCompletion(string $systemPrompt, string $userPrompt, string $requestedModel = 'claude-3-5-sonnet-20241022'): string
    {
        $apiKey = config('visionlab.ai.api_key') ?: env('ANTHROPIC_API_KEY');

        if (empty($apiKey)) {
            return "Demo Mode: AI Meeting summary placeholder. Add your Anthropic API key to generate a real meeting summary.";
        }

        try {
            $client = new Client();
            $response = $client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key'         => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    'model'      => $requestedModel,
                    'max_tokens' => 1000,
                    'system'     => $systemPrompt,
                    'messages'   => [
                        ['role' => 'user', 'content' => $userPrompt]
                    ],
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $content = $body['content'] ?? [];
            foreach ($content as $block) {
                if ($block['type'] === 'text') {
                    return $block['text'];
                }
            }
            return 'No summary generated.';
        } catch (\Throwable $e) {
            Log::error("AiService::getDirectCompletion error: " . $e->getMessage());
            return "Error generating summary: " . $e->getMessage();
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

    private function logAction(Workspace $workspace, ?int $userId, string $action, array $context): void
    {
        AiActionsLog::create([
            'user_id'       => $userId,
            'workspace_ref' => 'ws-' . $workspace->id,
            'action_type'   => substr($action, 0, 50),
            'diff_summary'  => json_encode($context),
            'mode'          => $context['mode'] ?? 'CHAT',
        ]);
    }
}
