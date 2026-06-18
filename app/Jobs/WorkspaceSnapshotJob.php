<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WorkspaceSnapshotJob implements ShouldQueue
{
    use Queueable, \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public \App\Models\Workspace $workspace;
    public \App\Models\Submission $submission;

    /**
     * Create a new job instance.
     */
    public function __construct(\App\Models\Workspace $workspace, \App\Models\Submission $submission)
    {
        $this->workspace = $workspace;
        $this->submission = $submission;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->workspace->storage_path || !\Illuminate\Support\Facades\Storage::exists($this->workspace->storage_path)) {
            \Illuminate\Support\Facades\Log::warning("WorkspaceSnapshotJob: Workspace storage path not found for workspace {$this->workspace->id}");
            return;
        }

        $sourceDir = \Illuminate\Support\Facades\Storage::path($this->workspace->storage_path);
        $zipPath = 'snapshots/workspace_' . $this->workspace->id . '_' . time() . '.zip';
        $fullZipPath = \Illuminate\Support\Facades\Storage::path($zipPath);

        // Ensure directory exists
        \Illuminate\Support\Facades\Storage::makeDirectory('snapshots');

        // Basic zip creation using PHP ZipArchive
        $zip = new \ZipArchive();
        if ($zip->open($fullZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($sourceDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            $this->submission->update([
                'workspace_snapshot_path' => $zipPath,
            ]);
        }
    }
}
