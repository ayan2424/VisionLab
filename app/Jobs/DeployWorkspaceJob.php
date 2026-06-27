<?php

namespace App\Jobs;

use App\Models\Deployment;
use App\Models\Workspace;
use App\Models\UserBadge;
use App\Services\Deployment\RailwayProvider;
use App\Services\Deployment\VercelProvider;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeployWorkspaceJob implements ShouldQueue
{
    use Queueable, \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public int $deploymentId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $deploymentId)
    {
        $this->deploymentId = $deploymentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $deployment = Deployment::find($this->deploymentId);
        if (!$deployment) {
            Log::error("DeployWorkspaceJob: Deployment with ID {$this->deploymentId} not found.");
            return;
        }

        $workspace = $deployment->workspace;
        if (!$workspace) {
            $deployment->update([
                'status'        => 'failed',
                'error_summary' => 'Workspace not found.',
            ]);
            return;
        }

        $deployment->update(['status' => 'building']);

        try {
            $provider = $deployment->provider === 'railway'
                ? new RailwayProvider()
                : new VercelProvider();

            $result = $provider->deploy($workspace);

            $deployment->update([
                'status'        => 'deployed',
                'deployment_id' => $result['id'],
                'public_url'    => $result['url'],
                'deployed_at'   => now(),
            ]);

            // Phase 9: Gamification - Award Cloud Pioneer Badge
            UserBadge::awardOnce(
                $deployment->user_id,
                'first_deployment',
                'Cloud Pioneer',
                'Successfully deployed a project to the cloud.',
                '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>'
            );

            Log::info("DeployWorkspaceJob: Workspace {$workspace->id} deployed successfully via {$deployment->provider}");
        } catch (\Throwable $e) {
            $deployment->update([
                'status'        => 'failed',
                'error_summary' => $e->getMessage(),
            ]);
            Log::error("DeployWorkspaceJob: Deployment failed for workspace {$workspace->id}: " . $e->getMessage());
        }
    }
}
