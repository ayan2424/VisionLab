<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('submissions:check-late')->hourly();
Schedule::command('assignments:due-reminders')->everySixHours();
Schedule::command('workspaces:stale-heartbeat')->everyFiveMinutes();
Schedule::command('workspaces:cleanup')->daily();
Schedule::command('daily:update-streaks')->dailyAt('00:00');

// Scheduled Push Notifications Reminder
Schedule::call(function () {
    $students = \App\Models\User::where('role', 'student')->whereHas('pushSubscriptions')->get();
    foreach($students as $student) {
        $student->notify(new \App\Notifications\ScheduledReminderNotification('Don\'t forget to check your pending assignments and keep up your daily streak!'));
    }
})->dailyAt('10:00');
