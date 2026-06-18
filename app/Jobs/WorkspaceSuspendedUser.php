<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class WorkspaceSuspendedUser implements ShouldQueue
{
    use Queueable, \Illuminate\Foundation\Bus\Dispatchable, \Illuminate\Queue\InteractsWithQueue, \Illuminate\Queue\SerializesModels;

    public int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\CodeServerManager $manager): void
    {
        $user = \App\Models\User::with('workspaces')->find($this->userId);
        if (!$user) return;

        foreach ($user->workspaces as $workspace) {
            if ($workspace->status === 'running') {
                $manager->stopWorkspace($workspace);
            }
            $workspace->update(['status' => 'error']); // or suspended if there was a suspended status
        }

        \Illuminate\Support\Facades\Log::info("Suspended workspaces for user {$this->userId}");
    }
}
