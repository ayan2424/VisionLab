<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CodeExecutionController extends Controller
{
    private const PISTON_API = 'https://emkc.org/api/v2/piston';
    private const TIMEOUT_SECONDS = 15;

    /**
     * Execute code via the Piston API (sandboxed, polyglot).
     * Falls back to a friendly error if Piston is unreachable.
     *
     * POST /api/code/run
     * Body: { language, source_code, stdin? }
     */
    public function run(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'language'    => 'required|string|max:30',
            'source_code' => 'required|string|max:65535',
            'stdin'       => 'nullable|string|max:4096',
            'filename'    => 'nullable|string|max:120',
        ]);

        $langMap  = WorkspaceController::pistonLanguage($validated['language']);
        $filename = $validated['filename'] ?? $this->defaultFilename($validated['language']);

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->post(self::PISTON_API . '/execute', [
                    'language' => $langMap['language'],
                    'version'  => $langMap['version'],
                    'files'    => [
                        [
                            'name'    => $filename,
                            'content' => $validated['source_code'],
                        ],
                    ],
                    'stdin'       => $validated['stdin'] ?? '',
                    'args'        => [],
                    'compile_timeout' => 10000,
                    'run_timeout'     => 10000,
                    'compile_memory_limit' => -1,
                    'run_memory_limit'     => -1,
                ]);

            if ($response->failed()) {
                return $this->errorResponse(
                    'Piston API returned status ' . $response->status() . '. Please try again.',
                    $response->status()
                );
            }

            $data = $response->json();
            $run  = $data['run']     ?? [];
            $comp = $data['compile'] ?? null;

            // Compile error takes priority
            if ($comp && ! empty($comp['stderr'])) {
                return response()->json([
                    'success'   => false,
                    'stdout'    => '',
                    'stderr'    => $comp['stderr'],
                    'exit_code' => $comp['code'] ?? 1,
                    'language'  => $langMap['language'],
                    'version'   => $data['language'] ?? $langMap['language'],
                    'wall_time' => null,
                    'stage'     => 'compile',
                ]);
            }

            return response()->json([
                'success'   => ($run['code'] ?? 1) === 0,
                'stdout'    => $run['stdout']  ?? '',
                'stderr'    => $run['stderr']  ?? '',
                'exit_code' => $run['code']    ?? 0,
                'language'  => $langMap['language'],
                'version'   => $data['language'] ?? $langMap['language'],
                'wall_time' => null,
                'stage'     => 'run',
            ]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('Piston API unreachable', ['error' => $e->getMessage()]);
            return $this->errorResponse(
                'Code runner is temporarily unavailable. Please check your internet connection or try again in a moment.'
            );
        } catch (\Throwable $e) {
            Log::error('Code execution error', ['error' => $e->getMessage()]);
            return $this->errorResponse('An unexpected error occurred during execution.');
        }
    }

    /**
     * Return the available Piston runtimes (proxied from API).
     * GET /api/code/runtimes
     */
    public function runtimes(): JsonResponse
    {
        try {
            $response = Http::timeout(8)->get(self::PISTON_API . '/runtimes');
            return response()->json($response->json());
        } catch (\Throwable) {
            return response()->json([]);
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function defaultFilename(string $lang): string
    {
        return match ($lang) {
            'python'     => 'main.py',
            'javascript' => 'index.js',
            'typescript' => 'index.ts',
            'php'        => 'main.php',
            'java'       => 'Main.java',
            'c'          => 'main.c',
            'cpp'        => 'main.cpp',
            'rust'       => 'main.rs',
            'go'         => 'main.go',
            'ruby'       => 'main.rb',
            'bash'       => 'main.sh',
            default      => 'main.txt',
        };
    }

    private function errorResponse(string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success'   => false,
            'stdout'    => '',
            'stderr'    => $message,
            'exit_code' => 1,
            'language'  => 'unknown',
            'version'   => null,
            'wall_time' => null,
            'stage'     => 'run',
        ], $status > 499 ? 502 : 200);
    }
}
