<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Carbon\Carbon;
use App\Services\GamificationService;
use App\Models\AnalyticsEvent;

class UpdateUserStreak
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Only track streaks for students
        if (!$user->isStudent()) {
            return;
        }

        $now = Carbon::now();
        $lastActivity = $user->last_activity_at;

        if (!$lastActivity) {
            // First time ever logging in (or first time since this feature launched)
            $user->current_streak = 1;
            $user->longest_streak = 1;
        } else {
            // Check if last activity was yesterday
            $daysDiff = $lastActivity->startOfDay()->diffInDays($now->startOfDay());

            if ($daysDiff == 1) {
                // Logged in yesterday, increment streak!
                $user->current_streak += 1;
                
                if ($user->current_streak > $user->longest_streak) {
                    $user->longest_streak = $user->current_streak;
                }
            } elseif ($daysDiff > 1) {
                // Streak broken
                $user->current_streak = 1;
            }
            // If $daysDiff == 0, they already logged in today, do nothing.
        }

        $user->last_activity_at = $now;
        $user->save();

        // Track the login event for global analytics
        AnalyticsEvent::track('user_login', [], $user->id, 'user', $user->id);

        // Evaluate streak badges
        if (class_exists(GamificationService::class)) {
            GamificationService::evaluateBadges($user);
        }
    }
}
