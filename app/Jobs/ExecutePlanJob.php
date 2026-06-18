<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecutePlanJob implements ShouldQueue
{
    use Queueable, \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public int $patchId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $patchId)
    {
        $this->patchId = $patchId;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\CodeServerManager $manager): void
    {
        $patch = \App\Models\AiPendingPatch::with('workspace')->find($this->patchId);
        if (!$patch || $patch->status !== 'approved') {
            return;
        }

        // Create snapshot for rollback
        \App\Models\AiSnapshot::create([
            'workspace_id' => $patch->workspace_id,
            'session_id'   => $patch->session_id,
            'file_path'    => $patch->file_path,
            'content'      => $patch->original_content,
            'content_hash' => $patch->original_hash,
            'created_by'   => $patch->reviewer_id ?? $patch->created_by,
        ]);

        // Write file
        $success = $manager->writeFile($patch->workspace, $patch->file_path, $patch->patched_content);

        if ($success) {
            $patch->update(['status' => 'applied']);
            
            // Log audit trail
            activity('ai_operations')
                ->performedOn($patch)
                ->causedBy($patch->reviewer_id)
                ->withProperties(['file' => $patch->file_path])
                ->log('applied_patch');
        } else {
            $patch->update(['status' => 'error']);
        }
    }
}
