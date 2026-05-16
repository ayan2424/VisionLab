<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── Scheduled Tasks ──────────────────────────────────────────────

// Send push notifications for assignments due within 24 hours (daily at 8 AM)
Schedule::command('notify:due-assignments --hours=24')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/notify-assignments.log'));

// Clean up workspace containers inactive for 24+ hours (every 6 hours)
Schedule::command('workspaces:cleanup --inactive=24')
    ->everySixHours()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/workspace-cleanup.log'));

// Clear expired cache entries (daily)
Schedule::command('cache:prune-stale-tags')->daily();
