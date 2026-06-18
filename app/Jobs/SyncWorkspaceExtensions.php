<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncWorkspaceExtensions implements ShouldQueue
{
    use Queueable, \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public int $workspaceId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $workspaceId)
    {
        $this->workspaceId = $workspaceId;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\CodeServerManager $csm): void
    {
        $workspace = \App\Models\Workspace::find($this->workspaceId);
        if (!$workspace) return;

        // If the workspace is not currently running, we don't need to sync active container.
        // It will pick up the extensions on its next boot sequence.
        if ($workspace->status !== 'running') {
            \App\Models\WorkspaceExtension::where('workspace_id', $this->workspaceId)
                ->where('sync_status', 'pending')
                ->update(['sync_status' => 'synced']);
            return;
        }

        $pendingExtensions = \App\Models\WorkspaceExtension::with('extension')
            ->where('workspace_id', $this->workspaceId)
            ->where('sync_status', 'pending')
            ->get();

        foreach ($pendingExtensions as $wsExt) {
            $identifier = $wsExt->extension->package_identifier;
            
            if ($wsExt->is_enabled) {
                // If it's a custom extension, we'd install from its custom VSIX artifact path.
                // For now we assume identifierOrPath is either the marketplace identifier or absolute path.
                $target = $wsExt->extension->is_builtin ? $identifier : ($wsExt->extension->artifact_path ?? $identifier);
                
                $success = $csm->installExtension($workspace, $target);
                
                if ($success) {
                    $wsExt->update(['sync_status' => 'synced']);
                    \Illuminate\Support\Facades\Log::info("Synced extension {$identifier} to workspace {$this->workspaceId}");
                } else {
                    $wsExt->update(['sync_status' => 'failed']);
                }
            } else {
                $success = $csm->uninstallExtension($workspace, $identifier);
                if ($success) {
                    $wsExt->update(['sync_status' => 'synced']);
                    \Illuminate\Support\Facades\Log::info("Uninstalled extension {$identifier} from workspace {$this->workspaceId}");
                } else {
                    $wsExt->update(['sync_status' => 'failed']);
                }
            }
        }
    }
}
