<?php

namespace App\Jobs;

use App\Models\Recording;
use App\Models\User;
use App\Notifications\RecordingReadyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProcessRecordingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 300;

    public function __construct(public readonly int $recordingId) {}

    public function handle(): void
    {
        $recording = Recording::findOrFail($this->recordingId);

        // TODO: integrate with Jitsi recording webhook/S3 path
        // For now: move status to pending_approval so admin can review
        $recording->update(['status' => 'pending_approval']);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new RecordingReadyNotification($recording));
    }

    public function failed(\Throwable $e): void
    {
        Recording::find($this->recordingId)?->update(['status' => 'failed']);
    }
}