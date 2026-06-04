<?php

namespace App\Services;

use App\Models\AiSetting;
use App\Models\Room;
use GuzzleHttp\Client;

class AiService
{
    private AiSandbox $sandbox;

    public function __construct(AiSandbox $sandbox)
    {
        $this->sandbox = $sandbox;
    }

    /**
     * Handle an OpenAI-compatible chat completion request and proxy to the right provider.
     * Returns a generator that yields SSE strings.
     */
    public function handleChatCompletion(Room $room, array $messages, string $mode = 'CHAT', string $model = 'claude-3-5-sonnet-20241022')
    {
        $apiKey = $this->resolveApiKey($model);

        if (empty($apiKey)) {
            yield 'data: '.json_encode(['choices' => [['delta' => ['content' => "Error: No API key configured for this model's provider. Please contact the administrator."]]]])."\n\n";
            yield "data: [DONE]\n\n";

            return;
        }

        // Inject Memory if exists
        $systemPrompt = $this->getSystemPrompt($mode);
        $memoryContent = $this->sandbox->readFile($room, '.VisionLab_memory.md');
        if ($memoryContent) {
            $systemPrompt = "User's Memory:\n".$memoryContent."\n\n".$systemPrompt;
        }

        if (str_starts_with($model, 'claude')) {
            yield from $this->callAnthropic($apiKey, $model, $messages, $systemPrompt);
        } elseif (str_starts_with($model, 'gemini')) {
            yield from $this->callGemini($apiKey, $model, $messages, $systemPrompt);
        } elseif (str_starts_with($model, 'deepseek')) {
            yield from $this->callDeepSeek($apiKey, $model, $messages, $systemPrompt);
        } else {
            yield from $this->callOpenAI($apiKey, $model, $messages, $systemPrompt);
        }
    }

    private function resolveApiKey(string $model): ?string
    {
        if (str_starts_with($model, 'claude')) {
            return AiSetting::getValue('anthropic_api_key');
        }
        if (str_starts_with($model, 'gemini')) {
            return AiSetting::getValue('google_api_key');
        }
        if (str_starts_with($model, 'deepseek')) {
            return AiSetting::getValue('deepseek_api_key');
        }

        return AiSetting::getValue('openai_api_key');
    }

    private function callAnthropic(string $apiKey, string $model, array $messages, string $systemPrompt)
    {
        $anthropicMessages = [];
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                continue;
            }
            $anthropicMessages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'assistant',
                'content' => $msg['content'],
            ];
        }

        $payload = [
            'model' => $model,
            'max_tokens' => (int) AiSetting::getValue('max_tokens_per_request', 4096),
            'system' => $systemPrompt,
            'messages' => $anthropicMessages,
            'stream' => true,
        ];

        $client = new Client;
        $response = $client->post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => $payload,
            'stream' => true,
        ]);

        $body = $response->getBody();
        while (! $body->eof()) {
            $chunk = $body->read(1024);
            $lines = explode("\n", $chunk);
            foreach ($lines as $line) {
                if (str_starts_with($line, 'data: ')) {
                    $data = json_decode(substr($line, 6), true);
                    if ($data && isset($data['type']) && $data['type'] === 'content_block_delta') {
                        $text = $data['delta']['text'] ?? '';
                        yield 'data: '.json_encode(['choices' => [['delta' => ['content' => $text]]]])."\n\n";
                    }
                }
            }
        }
        yield "data: [DONE]\n\n";
    }

    private function callGemini(string $apiKey, string $model, array $messages, string $systemPrompt)
    {
        $geminiMessages = [];
        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                continue;
            }
            $geminiMessages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]],
            ];
        }

        $payload = [
            'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents' => $geminiMessages,
        ];

        $client = new Client;
        $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:streamGenerateContent?key={$apiKey}", [
            'json' => $payload,
            'stream' => true,
        ]);

        $body = $response->getBody();
        while (! $body->eof()) {
            $chunk = $body->read(2048);
            if (preg_match('/"text":\s*"([^"]+)"/', $chunk, $matches)) {
                $text = stripcslashes($matches[1]);
                yield 'data: '.json_encode(['choices' => [['delta' => ['content' => $text]]]])."\n\n";
            }
        }
        yield "data: [DONE]\n\n";
    }

    private function callOpenAI(string $apiKey, string $model, array $messages, string $systemPrompt)
    {
        $openAiMessages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($messages as $msg) {
            if ($msg['role'] !== 'system') {
                $openAiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }

        $client = new Client;
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'messages' => $openAiMessages,
                'stream' => true,
                'max_tokens' => (int) AiSetting::getValue('max_tokens_per_request', 4096),
            ],
            'stream' => true,
        ]);

        $body = $response->getBody();
        while (! $body->eof()) {
            yield $body->read(2048);
        }
    }

    private function callDeepSeek(string $apiKey, string $model, array $messages, string $systemPrompt)
    {
        $deepSeekMessages = [['role' => 'system', 'content' => $systemPrompt]];
        foreach ($messages as $msg) {
            if ($msg['role'] !== 'system') {
                $deepSeekMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }

        $client = new Client;
        $response = $client->post('https://api.deepseek.com/chat/completions', [
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'messages' => $deepSeekMessages,
                'stream' => true,
                'max_tokens' => (int) AiSetting::getValue('max_tokens_per_request', 4096),
            ],
            'stream' => true,
        ]);

        $body = $response->getBody();
        while (! $body->eof()) {
            yield $body->read(2048);
        }
    }

    private function getSystemPrompt(string $mode): string
    {
        return match ($mode) {
            'PLAN' => 'You are VisionLab Planner. Create step-by-step implementation plans. Do NOT use the propose_patch tool. Only text.',
            'AGENT' => "You are VisionLab Agent. You must fulfill the user's request by reading necessary files, and then proposing patches using the `propose_patch` tool.",
            default => 'You are an AI coding assistant in the VisionLab IDE.'
        };
    }
}
