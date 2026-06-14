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
     * Search the codebase securely using grep.
     */
    public function searchCodebase(Workspace $workspace, string $query): array
    {
        $basePath = storage_path('workspaces' . DIRECTORY_SEPARATOR . 'ws-' . $workspace->id);
        
        if (!is_dir($basePath)) {
            return [];
        }

        // Use ripgrep or grep recursively
        // -r = recursive, -n = line numbers, -i = ignore case, -I = ignore binaries
        $process = new Process(['grep', '-rniI', $query, $basePath]);
        $process->setTimeout(10);
        $process->run();

        if (!$process->isSuccessful() && $process->getExitCode() !== 1) { // 1 means no match, other codes are errors
            return [];
        }

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
                    'file'    => $filePath,
                    'line'    => $parts[1],
                    'content' => $parts[2],
                ];
            }
        }

        return $results;
    }

    /**
     * Prepare a patch to be reviewed by the user.
     */
    public function preparePatch(Workspace $workspace, ?int $sessionId, string $relativePath, string $searchBlock, string $replaceBlock): ?array
    {
        $currentContent = $this->readFile($workspace, $relativePath);
        if ($currentContent === null) {
            // File might not exist. If searchBlock is empty, it's a new file.
            if (empty($searchBlock)) {
                $currentContent = '';
            } else {
                return ['error' => "File not found: {$relativePath}"];
            }
        }

        // Extremely naive patch replacement for the MVP.
        // A real system would use a diff library to handle fuzzy matching.
        $patchedContent = str_replace($searchBlock, $replaceBlock, $currentContent);

        if ($patchedContent === $currentContent && !empty($searchBlock)) {
            return ['error' => "Could not find the exact search block in the file. Context might have changed."];
        }

        // Generate a unified diff preview using diff command
        $tmpOriginal = tempnam(sys_get_temp_dir(), 'orig_');
        $tmpPatched = tempnam(sys_get_temp_dir(), 'patch_');
        file_put_contents($tmpOriginal, $currentContent);
        file_put_contents($tmpPatched, $patchedContent);

        $process = new Process(['diff', '-u', $tmpOriginal, $tmpPatched]);
        $process->run();
        $diffOutput = $process->getOutput();

        unlink($tmpOriginal);
        unlink($tmpPatched);

        // Store snapshot
        $snapshot = AiSnapshot::create([
            'workspace_id' => $workspace->id,
            'file_path'    => $relativePath,
            'content'      => $currentContent,
            'created_by'   => Auth::id() ?? 1,
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
            'created_by'       => Auth::id() ?? 1,
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
                'user_id'       => Auth::id() ?? 1,
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
                'user_id'       => Auth::id() ?? 1,
                'workspace_ref' => 'ws-' . $workspace->id,
                'action_type'   => 'rollback_patch',
                'file_path'     => $patch->file_path,
                'diff_summary'  => "Rolled back patch #{$patch->id}",
                'mode'          => 'AGENT',
            ]);
        }

        return $success;
    }
}
