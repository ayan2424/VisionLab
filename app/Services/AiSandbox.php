<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\AiSnapshot;
use App\Models\AiPendingPatch;
use App\Models\AiActionsLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;

class AiSandbox
{
    private CodeServerManager $manager;

    public function __construct(CodeServerManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Read a file securely from the workspace.
     */
    public function readFile(Workspace $workspace, string $relativePath): ?string
    {
        return $this->manager->readFile($workspace, $relativePath);
    }

    /**
     * List a directory securely within the workspace.
     */
    public function listDirectory(Workspace $workspace, string $relativePath = ''): array
    {
        return $this->manager->listFiles($workspace, $relativePath);
    }

    /**
     * Search the codebase securely using grep, with a native PHP fallback.
     */
    public function searchCodebase(Workspace $workspace, string $query): array
    {
        $basePath = storage_path('workspaces' . DIRECTORY_SEPARATOR . 'ws-' . $workspace->id);
        
        if (!is_dir($basePath)) {
            return [];
        }

        // 1. Try using command-line grep
        try {
            // -r = recursive, -n = line numbers, -i = ignore case, -I = ignore binaries
            $process = new Process(['grep', '-rniI', $query, $basePath]);
            $process->setTimeout(10);
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                $lines = explode("\n", trim($output));
                $results = [];

                foreach ($lines as $line) {
                    if (empty($line)) continue;
                    
                    // Format: /path/to/file:line:content
                    $parts = explode(':', $line, 3);
                    if (count($parts) >= 3) {
                        $filePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $parts[0]);
                        $results[] = [
                            'file'    => str_replace('\\', '/', $filePath),
                            'line'    => $parts[1],
                            'content' => $parts[2],
                        ];
                    }
                }
                return $results;
            }
        } catch (\Throwable $e) {
            Log::info("Command-line grep failed or is not available, falling back to native search.");
        }

        // 2. Native PHP Search Fallback (Windows Dev Compatibility)
        $results = [];
        try {
            $directory = new \RecursiveDirectoryIterator($basePath);
            $iterator = new \RecursiveIteratorIterator($directory);
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $filePath = $file->getPathname();
                    $relPath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $filePath);
                    $normalized = strtolower(str_replace('\\', '/', $relPath));
                    
                    // Skip non-project folders
                    $blocked = ['vendor/', 'storage/', 'bootstrap/', 'node_modules/', '.git/'];
                    $isBlocked = false;
                    foreach ($blocked as $pattern) {
                        if (str_contains($normalized, $pattern)) {
                            $isBlocked = true;
                            break;
                        }
                    }
                    if ($isBlocked) continue;

                    $content = file_get_contents($filePath);
                    if ($content !== false && str_contains(strtolower($content), strtolower($query))) {
                        $lines = explode("\n", $content);
                        foreach ($lines as $index => $lineContent) {
                            if (str_contains(strtolower($lineContent), strtolower($query))) {
                                $results[] = [
                                    'file'    => str_replace('\\', '/', $relPath),
                                    'line'    => $index + 1,
                                    'content' => trim($lineContent),
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("Native search fallback failed: " . $e->getMessage());
        }

        return array_slice($results, 0, 50); // Cap results at 50 matching lines
    }

    /**
     * Prepare a patch to be reviewed by the user.
     */
    public function preparePatch(Workspace $workspace, ?int $sessionId, string $relativePath, string $searchBlock, string $replaceBlock): ?array
    {
        // Sandbox check: Prevent AI from touching critical internal directories
        $normalizedPath = strtolower(str_replace('\\', '/', $relativePath));
        $blocked = ['.env', 'vendor/', 'storage/', 'bootstrap/', 'node_modules/', '.git/'];
        foreach ($blocked as $pattern) {
            if (str_contains($normalizedPath, $pattern)) {
                return ['error' => "Security Sandbox: AI is not permitted to modify {$pattern}"];
            }
        }

        $currentContent = $this->readFile($workspace, $relativePath);
        if ($currentContent === null) {
            if (empty($searchBlock)) {
                $currentContent = '';
            } else {
                return ['error' => "File not found: {$relativePath}"];
            }
        }

        $patchedContent = str_replace($searchBlock, $replaceBlock, $currentContent);

        if ($patchedContent === $currentContent && !empty($searchBlock)) {
            return ['error' => "Could not find the exact search block in the file. Context might have changed."];
        }

        // Generate unified diff
        $diffOutput = '';
        try {
            $tmpOriginal = tempnam(sys_get_temp_dir(), 'orig_');
            $tmpPatched = tempnam(sys_get_temp_dir(), 'patch_');
            file_put_contents($tmpOriginal, $currentContent);
            file_put_contents($tmpPatched, $patchedContent);

            $process = new Process(['diff', '-u', $tmpOriginal, $tmpPatched]);
            $process->run();
            $diffOutput = $process->getOutput();

            unlink($tmpOriginal);
            unlink($tmpPatched);
        } catch (\Throwable $e) {
            Log::info("Command-line diff failed, falling back to native diffing.");
        }

        if (empty($diffOutput)) {
            $diffOutput = $this->getPhpNativeDiff($currentContent, $patchedContent);
        }

        // Store snapshot
        $snapshot = AiSnapshot::create([
            'workspace_id' => $workspace->id,
            'file_path'    => $relativePath,
            'content'      => $currentContent,
            'created_by'   => Auth::id() ?? $workspace->student_id ?? 1,
        ]);

        // Store pending patch
        $patch = AiPendingPatch::create([
            'workspace_id'     => $workspace->id,
            'session_id'       => $sessionId,
            'file_path'        => $relativePath,
            'original_content' => $currentContent,
            'patched_content'  => $patchedContent,
            'diff'             => $diffOutput,
            'status'           => 'pending',
            'created_by'       => Auth::id() ?? $workspace->student_id ?? 1,
        ]);

        return [
            'patch_id' => $patch->id,
            'diff'     => $diffOutput
        ];
    }

    /**
     * Apply an approved patch.
     */
    public function applyPatch(AiPendingPatch $patch): bool
    {
        if ($patch->status !== 'pending') {
            return false;
        }

        $workspace = Workspace::find($patch->workspace_id);
        if (!$workspace) return false;

        $success = $this->manager->writeFile($workspace, $patch->file_path, $patch->patched_content);

        if ($success) {
            $patch->update(['status' => 'approved']);

            AiActionsLog::create([
                'user_id'       => Auth::id() ?? $workspace->student_id ?? 1,
                'workspace_ref' => 'ws-' . $workspace->id,
                'action_type'   => 'apply_patch',
                'file_path'     => $patch->file_path,
                'diff_summary'  => "Applied patch #{$patch->id}",
                'mode'          => 'AGENT',
            ]);
        }

        return $success;
    }

    /**
     * Rollback a patch from a snapshot.
     */
    public function rollbackPatch(AiPendingPatch $patch): bool
    {
        $workspace = Workspace::find($patch->workspace_id);
        if (!$workspace) return false;

        $snapshot = AiSnapshot::where('workspace_id', $workspace->id)
            ->where('file_path', $patch->file_path)
            ->where('created_at', '<=', $patch->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$snapshot) {
            return false;
        }

        $success = $this->manager->writeFile($workspace, $patch->file_path, $snapshot->content);
        
        if ($success) {
            $patch->update(['status' => 'rejected']);
            
            AiActionsLog::create([
                'user_id'       => Auth::id() ?? $workspace->student_id ?? 1,
                'workspace_ref' => 'ws-' . $workspace->id,
                'action_type'   => 'rollback_patch',
                'file_path'     => $patch->file_path,
                'diff_summary'  => "Rolled back patch #{$patch->id}",
                'mode'          => 'AGENT',
            ]);
        }

        return $success;
    }

    /**
     * Generate native line-by-line diff.
     */
    private function getPhpNativeDiff(string $old, string $new): string
    {
        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $new);
        $diff = [];
        
        $i = 0; $j = 0;
        while ($i < count($oldLines) || $j < count($newLines)) {
            if (isset($oldLines[$i]) && isset($newLines[$j]) && trim($oldLines[$i]) === trim($newLines[$j])) {
                $diff[] = ' ' . $oldLines[$i];
                $i++;
                $j++;
            } else {
                if (isset($oldLines[$i])) {
                    $diff[] = '-' . $oldLines[$i];
                    $i++;
                }
                if (isset($newLines[$j])) {
                    $diff[] = '+' . $newLines[$j];
                    $j++;
                }
            }
        }
        return implode("\n", $diff);
    }
}
