<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GdprDataExportJob implements ShouldQueue
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
    public function handle(): void
    {
        $user = \App\Models\User::with(['workspaces', 'submissions'])->find($this->userId);
        if (!$user) return;

        $exportData = [
            'profile' => $user->toArray(),
            'workspaces' => $user->workspaces->toArray(),
            'submissions' => $user->submissions->toArray(),
        ];

        $exportPath = 'gdpr_exports/user_' . $user->id . '_' . time() . '.json';
        
        \Illuminate\Support\Facades\Storage::put($exportPath, json_encode($exportData, JSON_PRETTY_PRINT));

        // In a real app, notify the user that their export is ready with a signed download link.
        \Illuminate\Support\Facades\Log::info("GDPR export for user {$user->id} completed at {$exportPath}");
    }
}
