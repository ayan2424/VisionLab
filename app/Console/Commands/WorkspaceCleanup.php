<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WorkspaceCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workspaces:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old stopped or error workspaces to save space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Delete workspaces stopped/error for more than 30 days
        $oldWorkspaces = \App\Models\Workspace::whereIn('status', ['stopped', 'error'])
            ->where('updated_at', '<', now()->subDays(30))
            ->get();

        $count = 0;
        foreach ($oldWorkspaces as $workspace) {
            // Check if storage path exists and delete
            if ($workspace->storage_path && \Illuminate\Support\Facades\Storage::exists($workspace->storage_path)) {
                \Illuminate\Support\Facades\Storage::deleteDirectory($workspace->storage_path);
            }
            $workspace->delete();
            $count++;
        }

        $this->info("Cleaned up {$count} old workspaces.");
    }
}
