<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('submissions:check-late')->hourly();
Schedule::command('assignments:due-reminders')->everySixHours();
Schedule::command('workspaces:stale-heartbeat')->everyFiveMinutes();
Schedule::command('workspaces:cleanup')->daily();
Schedule::command('daily:update-streaks')->dailyAt('00:00');
