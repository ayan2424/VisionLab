<?php

namespace App\Listeners;

use App\Events\ExtensionIntegrityFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogExtensionIntegrityFailure
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExtensionIntegrityFailed $event): void
    {
        \App\Models\AnalyticsEvent::create([
            'event_type' => 'security_incident',
            'user_id' => $event->workspace->student_id,
            'course_id' => $event->workspace->course_id,
            'metadata' => [
                'incident' => 'extension_integrity_failure',
                'target' => $event->target,
                'expected_checksum' => $event->expectedChecksum,
                'actual_checksum' => $event->actualChecksum,
                'workspace_id' => $event->workspace->id,
            ],
        ]);
        
        \Illuminate\Support\Facades\Log::critical("Extension SHA256 Integrity Failure", [
            'target' => $event->target,
            'expected' => $event->expectedChecksum,
            'actual' => $event->actualChecksum,
            'workspace_id' => $event->workspace->id,
        ]);
    }
}
