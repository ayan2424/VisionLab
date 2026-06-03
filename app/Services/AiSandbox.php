<?php

namespace App\Services;

use App\Models\Room;
use App\Models\AiSnapshot;
use App\Models\AiPendingPatch;
use App\Models\AiActionsLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ExecutableFinder;

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
    public function readFile(Room $room, string $relativePath): ?string
    {
        return $this->manager->readFile($room, $relativePath);
    }

    /**
     * List a directory securely within the workspace.
     */
    public function listDirectory(Room $room, string $relativePath = ''): array
    {
        return $this->manager->listFiles($room, $relativePath);
    }

    /**
     * Search the codebase securely using grep.
     */
    public function searchCodebase(Room $room, string $query): array
    {
        $basePath = storage_path('workspaces' . DIRECTORY_SEPARATOR . $room->slug);
        
        if (!is_dir($basePath)) {
            return [];
        }

        // Use ripgrep or grep recursively
        // -r = recursive, -n = line numbers, -i = ignore case, -I = ignore binaries
        $finder = new ExecutableFinder();
        $rgPath = $finder->find('rg');

        if ($rgPath) {
            $process = new Process([$rgPath, '-ni', '--no-heading', $query, $basePath]);
        } else {
            $process = new Process(['grep', '-rniI', $query, $basePath]);
        }
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
    public function preparePatch(Room $room, ?int $sessionId, string $relativePath, string $searchBlock, string $replaceBlock): ?array
    {
        $currentContent = $this->readFile($room, $relativePath);
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

        // Extremely strict sandboxing: prevent dangerous PHP functions
        if (str_ends_with(strtolower($relativePath), '.php')) {
            $denied = config('ai.sandbox.denied_functions', []);
            foreach ($denied as $func) {
                // simple check for function calls
                if (preg_match("/\b{$func}\s*\(/i", $patchedContent)) {
                    return ['error' => "Sandbox violation: The function '{$func}' is not allowed for security reasons."];
                }
            }
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
            'workspace_id' => $room->id,
            'file_path'    => $relativePath,
            'content'      => $currentContent,
            'created_by'   => Auth::id() ?? 1,
        ]);

        // Store pending patch
        $patch = AiPendingPatch::create([
            'workspace_id'     => $room->id,
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

        $room = Room::find($patch->workspace_id);
        if (!$room) return false;

        $success = $this->manager->writeFile($room, $patch->file_path, $patch->patched_content);

        if ($success) {
            $patch->update(['status' => 'approved']);

            AiActionsLog::create([
                'user_id'       => Auth::id() ?? 1,
                'workspace_ref' => $room->slug,
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
        $room = Room::find($patch->workspace_id);
        if (!$room) return false;

        $snapshot = AiSnapshot::where('workspace_id', $room->id)
            ->where('file_path', $patch->file_path)
            ->where('created_at', '<=', $patch->created_at)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$snapshot) {
            return false;
        }

        $success = $this->manager->writeFile($room, $patch->file_path, $snapshot->content);
        
        if ($success) {
            $patch->update(['status' => 'rejected']);
            
            AiActionsLog::create([
                'user_id'       => Auth::id() ?? 1,
                'workspace_ref' => $room->slug,
                'action_type'   => 'rollback_patch',
                'file_path'     => $patch->file_path,
                'diff_summary'  => "Rolled back patch #{$patch->id}",
                'mode'          => 'AGENT',
            ]);
        }

        return $success;
    }
}
