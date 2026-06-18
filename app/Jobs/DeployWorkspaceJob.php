<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeployWorkspaceJob implements ShouldQueue
{
    use Queueable, \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public int $workspaceId;
    public string $providerName;

    /**
     * Create a new job instance.
     */
    public function __construct(int $workspaceId, string $providerName = 'vercel')
    {
        $this->workspaceId = $workspaceId;
        $this->providerName = $providerName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $workspace = \App\Models\Workspace::find($this->workspaceId);
        if (!$workspace) return;

        $provider = $this->providerName === 'railway' 
            ? new \App\Services\Deployment\RailwayProvider() 
            : new \App\Services\Deployment\VercelProvider();

        $result = $provider->deploy($workspace);

        \App\Models\Deployment::create([
            'workspace_id' => $workspace->id,
            'user_id' => $workspace->student_id,
            'provider' => $this->providerName,
            'provider_deployment_id' => $result['id'],
            'url' => $result['url'],
            'status' => $result['status'],
        ]);

        \Illuminate\Support\Facades\Log::info("Deployment queued for workspace {$workspace->id} via {$this->providerName}");
    }
}
