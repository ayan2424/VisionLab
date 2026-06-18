<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WorkspaceStaleHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workspaces:stale-heartbeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop workspaces that have been idle past their quota limits';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\CodeServerManager $manager)
    {
        // Default timeout is 60 minutes. Can be overridden by quota_data['timeout_minutes']
        $workspaces = \App\Models\Workspace::running()->get();

        $count = 0;
        foreach ($workspaces as $workspace) {
            $timeoutMinutes = $workspace->quota_data['timeout_minutes'] ?? 60;
            
            // If heartbeat_at is null, maybe it just started, give it 10 minutes grace
            if (is_null($workspace->heartbeat_at)) {
                if ($workspace->created_at->diffInMinutes(now()) > 10) {
                    $manager->stopWorkspace($workspace);
                    $count++;
                }
            } else if ($workspace->heartbeat_at->diffInMinutes(now()) >= $timeoutMinutes) {
                $manager->stopWorkspace($workspace);
                $count++;
            }
        }

        $this->info("Stopped {$count} stale workspaces.");
    }
}
