<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAgentController extends Controller
{
    private string $geminiKey;
    private string $geminiModel    = 'gemini-2.0-flash';
    private string $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->geminiKey = env('GEMINI_API_KEY', '');
    }

    // ── POST /api/ai/chat ─────────────────────────────────────────
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message'          => 'required|string|max:4000',
            'mode'             => 'sometimes|string|in:CHAT,PLAN,AGENT',
            'context.filename' => 'sometimes|string|max:255',
            'context.language' => 'sometimes|string|max:50',
            'context.code'     => 'sometimes|string|max:12000',
        ]);

        $mode     = strtoupper($validated['mode'] ?? 'CHAT');
        $message  = $validated['message'];
        $ctx      = $validated['context'] ?? [];
        $filename = $ctx['filename'] ?? 'untitled';
        $language = $ctx['language'] ?? 'python';
        $code     = $ctx['code']     ?? '';

        $systemPrompt = $this->buildSystemPrompt($mode, $filename, $language, $code);

        // Log AI action
        if ($request->user()) {
            \App\Models\AiActionsLog::create([
                'user_id'       => $request->user()->id,
                'workspace_ref' => $filename,
                'action_type'   => 'chat',
                'file_path'     => $filename,
                'diff_summary'  => substr($message, 0, 200),
                'mode'          => $mode,
            ]);
        }

        if ($this->geminiKey) {
            return $this->callGemini($systemPrompt, $message, $mode, $code);
        }

        return $this->demoResponse($message, $mode, $language, $code, $filename);
    }

    // ── POST /api/ai/propose-patch ────────────────────────────────
    public function proposePatch(Request $request)
    {
        $request->validate([
            'instruction' => 'required|string|max:2000',
            'code'        => 'required|string|max:12000',
            'language'    => 'sometimes|string',
            'filename'    => 'sometimes|string',
        ]);

        $instruction = $request->instruction;
        $original    = $request->code;
        $language    = $request->language ?? 'python';
        $filename    = $request->filename ?? 'file';

        if ($this->geminiKey) {
            return $this->callGeminiPatch(
                $this->buildPatchPrompt($instruction, $original, $language, $filename),
                $original, $filename
            );
        }

        return $this->demoPatch($instruction, $original, $filename, $language);
    }

    // ── POST /api/ai/apply-patch ──────────────────────────────────
    public function applyPatch(Request $request)
    {
        $request->validate(['patched' => 'required|string', 'file_id' => 'required|string']);
        return response()->json(['success' => true, 'applied' => true]);
    }

    // ── POST /api/ai/rollback ─────────────────────────────────────
    public function rollback(Request $request)
    {
        $request->validate(['file_id' => 'required|string']);
        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────
    //  Gemini HTTP helpers
    // ─────────────────────────────────────────────────────────────
    private function callGemini(string $system, string $user, string $mode, string $code): \Illuminate\Http\JsonResponse
    {
        try {
            $url      = "{$this->geminiEndpoint}/{$this->geminiModel}:generateContent?key={$this->geminiKey}";
            $response = Http::timeout(30)->post($url, [
                'system_instruction' => ['parts' => [['text' => $system]]],
                'contents'           => [['role' => 'user', 'parts' => [['text' => $user]]]],
                'generationConfig'   => [
                    'temperature'     => $mode === 'AGENT' ? 0.2 : 0.7,
                    'maxOutputTokens' => 2048,
                    'topP'            => 0.9,
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('Gemini error', ['status' => $response->status()]);
                return $this->errorResponse('AI service temporarily unavailable.');
            }

            $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
            return $this->parseGeminiResponse($rawText, $mode, $code);

        } catch (\Exception $e) {
            Log::error('Gemini call failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('AI service error: ' . $e->getMessage());
        }
    }

    private function parseGeminiResponse(string $text, string $mode, string $originalCode): \Illuminate\Http\JsonResponse
    {
        $snippet = null;
        if (preg_match('/```(?:\w+)?\n(.*?)```/s', $text, $m)) {
            $snippet = trim($m[1]);
        }

        if ($mode === 'AGENT' && $snippet) {
            $message = trim(preg_replace('/```(?:\w+)?\n.*?```/s', '', $text));
            return response()->json([
                'message'      => $message ?: 'I\'ve prepared a patch for your code.',
                'code_snippet' => null,
                'patch'        => ['original' => $originalCode, 'patched' => $snippet, 'file' => 'Current file'],
                'model'        => $this->geminiModel,
                'mode'         => $mode,
            ]);
        }

        return response()->json([
            'message'      => $this->formatMarkdown($text),
            'code_snippet' => $snippet,
            'patch'        => null,
            'model'        => $this->geminiModel,
            'mode'         => $mode,
        ]);
    }

    private function callGeminiPatch(string $prompt, string $original, string $filename): \Illuminate\Http\JsonResponse
    {
        try {
            $url      = "{$this->geminiEndpoint}/{$this->geminiModel}:generateContent?key={$this->geminiKey}";
            $response = Http::timeout(30)->post($url, [
                'contents'         => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.15, 'maxOutputTokens' => 2048],
            ]);

            if (!$response->successful()) {
                return $this->demoPatch('improve the code', $original, $filename, 'python');
            }

            $text    = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $patched = $original;
            if (preg_match('/```(?:\w+)?\n(.*?)```/s', $text, $m)) {
                $patched = trim($m[1]);
            }

            return response()->json([
                'patch' => ['original' => $original, 'patched' => $patched, 'file' => $filename],
                'model' => $this->geminiModel,
            ]);
        } catch (\Exception $e) {
            return $this->demoPatch('improve the code', $original, $filename, 'python');
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  System prompt builder
    // ─────────────────────────────────────────────────────────────
    private function buildSystemPrompt(string $mode, string $filename, string $language, string $code): string
    {
        $codeBlock = $code
            ? "Current file: `{$filename}` ({$language})\n```{$language}\n{$code}\n```\n\n"
            : '';

        $base = match ($mode) {
            'CHAT'  => "You are a senior software engineer assistant in a collaborative IDE called VisionLab. Answer code questions clearly and concisely. Format code with backtick blocks.",
            'PLAN'  => "You are an expert software architect in VisionLab. Analyze the code and produce a numbered, step-by-step implementation plan with headings and bullet points.",
            'AGENT' => "You are an autonomous code-editing agent in VisionLab. Respond with: 1) A brief explanation, 2) The COMPLETE rewritten file in a single ```{$language} code block. Never omit code or use placeholders.",
            default => "You are a helpful coding assistant.",
        };

        return "{$base}\n\n{$codeBlock}";
    }

    private function buildPatchPrompt(string $instruction, string $code, string $language, string $filename): string
    {
        return "You are a code editor. Task: {$instruction}\n\nFile: {$filename} ({$language})\n```{$language}\n{$code}\n```\n\nRespond ONLY with the complete rewritten file in a single ```{$language} code block.";
    }

    // ─────────────────────────────────────────────────────────────
    //  Demo responses (no API key)
    // ─────────────────────────────────────────────────────────────
    private function demoResponse(string $message, string $mode, string $language, string $code, string $filename): \Illuminate\Http\JsonResponse
    {
        $msg   = strtolower($message);
        $lines = $code ? substr_count($code, "\n") + 1 : 0;

        if ($mode === 'AGENT') {
            return response()->json([
                'message'      => "I've analyzed <strong>{$filename}</strong> ({$lines} lines). Here's my proposed change. Review the diff and click <em>Approve &amp; Apply</em> to write it.",
                'code_snippet' => null,
                'patch'        => ['original' => $code, 'patched' => $this->agentPatch($code, $message, $language), 'file' => $filename],
                'model'        => 'demo-mode',
                'mode'         => $mode,
            ]);
        }

        if ($mode === 'PLAN') {
            return response()->json([
                'message'      => $this->planResponse($message, $language, $lines),
                'code_snippet' => null,
                'patch'        => null,
                'model'        => 'demo-mode',
                'mode'         => $mode,
            ]);
        }

        // CHAT mode keyword dispatch
        [$reply, $snippet] = match (true) {
            str_contains($msg, 'explain') || str_contains($msg, 'what does') || str_contains($msg, 'how does')
                => [$this->explainCode($code, $language, $lines, $filename), null],

            str_contains($msg, 'bug') || str_contains($msg, 'error') || str_contains($msg, 'fix') || str_contains($msg, 'wrong')
                => [$this->bugHints($language), $this->bugFixSnippet($language)],

            str_contains($msg, 'optim') || str_contains($msg, 'faster') || str_contains($msg, 'performance') || str_contains($msg, 'slow')
                => [$this->optimizeResponse($language, $lines), null],

            str_contains($msg, 'test') || str_contains($msg, 'spec') || str_contains($msg, 'unit')
                => ["Here's a starter test suite for your <strong>{$language}</strong> code:", $this->testSnippet($language)],

            str_contains($msg, 'complex') || str_contains($msg, 'big o') || str_contains($msg, 'o(n')
                => [$this->complexityAnalysis(), null],

            str_contains($msg, 'refactor') || str_contains($msg, 'clean') || str_contains($msg, 'improve')
                => ["Here are <strong>3 refactoring opportunities</strong> in <strong>{$filename}</strong>:\n\n" . $this->refactorSuggestions(), null],

            str_contains($msg, 'hello') || str_contains($msg, 'hi') || str_contains($msg, 'hey')
                => ["Hello! I'm the VisionLabAgent. I can help you explain code, find bugs, optimize performance, write tests, and apply patches. What would you like to do with <strong>{$filename}</strong>?", null],

            default => [
                "I'm VisionLabin <strong>demo mode</strong>. Add your <code>GEMINI_API_KEY</code> to unlock full Gemini 2.0 Flash. "
                . "Try asking me to: <em>explain this code, find bugs, optimize performance, write tests, or refactor</em>. "
                . "Switch to <strong>AGENT</strong> mode to apply patches automatically.",
                null,
            ],
        };

        return response()->json([
            'message'      => $reply,
            'code_snippet' => $snippet,
            'patch'        => null,
            'model'        => 'demo-mode',
            'mode'         => $mode,
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  Demo content generators
    // ─────────────────────────────────────────────────────────────
    private function agentPatch(string $code, string $instruction, string $language): string
    {
        if (!$code) return $this->helloWorld($language);

        $msg = strtolower($instruction);

        if (str_contains($msg, 'comment') || str_contains($msg, 'document')) {
            $header = match ($language) {
                'python'                  => "# Auto-documented by VisionLabAgent · " . date('Y-m-d') . "\n\n",
                'javascript','typescript' => "// Auto-documented by VisionLabAgent · " . date('Y-m-d') . "\n\n",
                'php'                     => "<?php\n/** Auto-documented by VisionLabAgent · " . date('Y-m-d') . " */\n\n",
                default                   => "// Auto-documented by VisionLabAgent\n\n",
            };
            $body = ($language === 'php') ? ltrim(str_replace('<?php', '', $code)) : $code;
            return $header . $body;
        }

        if (str_contains($msg, 'type') && $language === 'python') {
            return preg_replace('/def (\w+)\(([^)]*)\)(?!.*->)/', 'def $1($2) -> None', $code) ?: $code;
        }

        if (str_contains($msg, 'async') && in_array($language, ['javascript','typescript'])) {
            return preg_replace('/function (\w+)/', 'async function $1', $code) ?: $code;
        }

        $header = match ($language) {
            'python'                  => "# Refactored by VisionLabAgent\n# Instruction: {$instruction}\n\n",
            'javascript','typescript' => "// Refactored by VisionLabAgent\n// Instruction: {$instruction}\n\n",
            'php'                     => "<?php\n/** Refactored by VisionLabAgent\n * Instruction: {$instruction}\n */\n\n",
            default                   => "// Refactored by VisionLabAgent: {$instruction}\n\n",
        };
        $body = ($language === 'php') ? ltrim(preg_replace('/^<\?php/', '', $code) ?? $code) : $code;
        return $header . $body;
    }

    private function planResponse(string $message, string $language, int $lines): string
    {
        $h = fn(string $s): string => htmlspecialchars($s);
        return "<strong>Implementation Plan</strong> for: <em>{$h($message)}</em>\n\n"
            . "<strong>1. Analysis</strong><br>File has {$lines} lines of {$language}. No breaking changes expected.\n\n"
            . "<strong>2. Design</strong><br>• Identify entry points and data flow<br>• Map dependencies<br>• Define interfaces/contracts\n\n"
            . "<strong>3. Steps</strong><br>• Step A: Scaffold core structure<br>• Step B: Implement business logic<br>• Step C: Add error handling<br>• Step D: Write tests\n\n"
            . "<strong>4. Validation</strong><br>• Run test suite · Manual smoke test · Code review\n\n"
            . "<strong>Effort:</strong> ~2–3 hours &nbsp;·&nbsp; <strong>Risk:</strong> Low<br><br>"
            . "<em>Switch to <strong>AGENT</strong> mode and say \"implement step A\" to start coding.</em>";
    }

    private function explainCode(string $code, string $language, int $lines, string $filename): string
    {
        preg_match_all('/(?:def|function|func|fn)\s+(\w+)\s*[\(\{]/', $code, $fm);
        preg_match_all('/class\s+(\w+)/', $code, $cm);
        $funcs   = implode(', ', array_slice($fm[1] ?? [], 0, 5)) ?: 'none detected';
        $classes = implode(', ', $cm[1] ?? []) ?: 'none';
        return "This <strong>{$language}</strong> file (<strong>{$filename}</strong>) has <strong>{$lines} lines</strong>. "
            . "Functions detected: <strong>{$funcs}</strong>. Classes: <strong>{$classes}</strong>. "
            . "The code follows standard {$language} patterns. Switch to <strong>PLAN</strong> for a deeper architectural analysis.";
    }

    private function bugHints(string $language): string
    {
        return "Common <strong>{$language}</strong> pitfalls to check:<br>" . match ($language) {
            'python'                  => "• Mutable default arguments (use <code>None</code>)<br>• Missing <code>if __name__ == '__main__':</code><br>• Unclosed file handles — use <code>with open()</code><br>• Integer division surprises",
            'javascript','typescript' => "• Unhandled promise rejections (add <code>.catch()</code>)<br>• <code>var</code> hoisting (prefer <code>const</code>/<code>let</code>)<br>• Loose equality (<code>==</code> vs <code>===</code>)<br>• Missing null checks",
            'php'                     => "• SQL injection — use PDO prepared statements<br>• Missing <code>isset()</code> on <code>$_GET</code>/<code>$_POST</code><br>• Loose comparisons with <code>==</code>",
            'java'                    => "• NullPointerException — add null checks<br>• Resource leaks — use try-with-resources<br>• Raw generics usage",
            default                   => "• Null/undefined references<br>• Unhandled exceptions<br>• Off-by-one errors<br>• Resource leaks",
        };
    }

    private function bugFixSnippet(string $language): string
    {
        return match ($language) {
            'python'                  => "# Safe mutable default argument pattern\ndef process(items=None):\n    if items is None:\n        items = []\n    return [item.strip() for item in items]",
            'javascript','typescript' => "// Safe async pattern\nasync function fetchData(url) {\n  try {\n    const res = await fetch(url);\n    if (!res.ok) throw new Error(`HTTP \${res.status}`);\n    return await res.json();\n  } catch (err) {\n    console.error('Fetch failed:', err);\n    return null;\n  }\n}",
            'php'                     => "<?php\n// Safe PDO query\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE id = ?');\n\$stmt->execute([\$userId]);\n\$user = \$stmt->fetch(PDO::FETCH_ASSOC);",
            default                   => "// Always validate inputs and handle exceptions\ntry {\n    // your logic here\n} catch (Exception e) {\n    // handle gracefully\n}",
        };
    }

    private function optimizeResponse(string $language, int $lines): string
    {
        return "<strong>Performance Analysis</strong> ({$lines}-line {$language} file):\n\n"
            . "• <strong>Algorithm:</strong> Nested loops O(n²) → reduce to O(n) with hash maps\n"
            . "• <strong>Memory:</strong> Avoid large in-memory datasets; use generators/streams\n"
            . "• <strong>I/O:</strong> Batch DB queries, cache repeated lookups\n"
            . "• <strong>Async:</strong> Parallelise independent operations\n\n"
            . "<em>Switch to <strong>AGENT</strong> mode and say <em>\"profile and optimize this\"</em> for a full rewrite.</em>";
    }

    private function testSnippet(string $language): string
    {
        return match ($language) {
            'python' => "import unittest\n\nclass TestCode(unittest.TestCase):\n\n    def test_basic(self):\n        # Arrange → Act → Assert\n        result = True  # replace with your call\n        self.assertTrue(result)\n\n    def test_edge_case(self):\n        self.assertIsNone(None)\n\nif __name__ == '__main__':\n    unittest.main()",
            'javascript','typescript' => "import { describe, it, expect } from 'vitest';\n\ndescribe('MyModule', () => {\n  it('should return expected value', () => {\n    const result = 2 + 2; // replace with your call\n    expect(result).toBe(4);\n  });\n\n  it('handles edge cases', () => {\n    expect(null).toBeNull();\n  });\n});",
            'php' => "<?php\nuse PHPUnit\\Framework\\TestCase;\n\nclass MyTest extends TestCase {\n    public function test_basic(): void {\n        \$result = true; // replace with your call\n        \$this->assertTrue(\$result);\n    }\n}",
            default => "// Write your tests here using your language's standard test runner",
        };
    }

    private function complexityAnalysis(): string
    {
        return "| Operation | Time | Space |\n|---|---|---|\n"
            . "| Best case  | O(1)   | O(1) |\n"
            . "| Average    | O(n)   | O(n) |\n"
            . "| Worst case | O(n²) | O(n) |\n\n"
            . "<em>Share a specific function in <strong>AGENT</strong> mode for a precise line-by-line analysis.</em>";
    }

    private function refactorSuggestions(): string
    {
        return "1. <strong>Extract functions</strong> — Break functions &gt;20 lines into single-responsibility units.\n\n"
            . "2. <strong>Remove magic numbers</strong> — Replace hardcoded values with named constants.\n\n"
            . "3. <strong>Early returns</strong> — Use guard clauses to reduce nesting depth.\n\n"
            . "<em>Switch to <strong>AGENT</strong> mode and say <em>\"apply these refactors\"</em> to have the AI rewrite the file.</em>";
    }

    private function helloWorld(string $language): string
    {
        return match ($language) {
            'python'                  => "# VisionLab\nprint('Hello, World!')",
            'javascript','typescript' => "// VisionLab\nconsole.log('Hello, World!');",
            'php'                     => "<?php\necho 'Hello, World!';",
            'java'                    => "public class Main {\n    public static void main(String[] args) {\n        System.out.println(\"Hello, World!\");\n    }\n}",
            'rust'                    => "fn main() {\n    println!(\"Hello, World!\");\n}",
            default                   => "console.log('Hello, World!');",
        };
    }

    private function demoPatch(string $instruction, string $original, string $filename, string $language): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'patch' => ['original' => $original, 'patched' => $this->agentPatch($original, $instruction, $language), 'file' => $filename],
            'model' => 'demo-mode',
        ]);
    }

    private function formatMarkdown(string $text): string
    {
        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/\*(.*?)\*/s',      '<em>$1</em>',         $text) ?? $text;
        $text = preg_replace('/`([^`]+)`/',        '<code>$1</code>',     $text) ?? $text;
        $text = preg_replace('/```[\s\S]*?```/',   '',                    $text) ?? $text;
        $text = preg_replace('/^\d+\.\s/m',        '<br>• ',              $text) ?? $text;
        return nl2br(trim($text));
    }

    private function errorResponse(string $message): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => $message, 'code_snippet' => null, 'patch' => null, 'model' => 'error', 'mode' => 'CHAT'], 200);
    }
}
