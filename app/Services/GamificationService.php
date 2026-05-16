<?php

namespace App\Services;

use App\Models\CodingSession;
use App\Models\UserBadge;
use App\Models\User;
use Carbon\Carbon;

class GamificationService
{
    /**
     * Log a day of activity for a user.
     */
    public function logActivity(User $user, int $durationMinutes = 0, int $commits = 0)
    {
        $today = Carbon::today();
        
        $session = CodingSession::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today->format('Y-m-d')]
        );

        $session->increment('duration_minutes', $durationMinutes);
        $session->increment('commits_count', $commits);

        $this->evaluateStreaks($user);
    }

    /**
     * Calculate and award badges based on consecutive days.
     */
    protected function evaluateStreaks(User $user)
    {
        $sessions = CodingSession::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        if ($sessions->isEmpty()) return;

        $streak = 0;
        $currentDate = Carbon::today();

        foreach ($sessions as $session) {
            if (Carbon::parse($session->date)->isSameDay($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } elseif (Carbon::parse($session->date)->isSameDay($currentDate->copy()->addDay())) {
                // They already coded today, handled above
                continue;
            } else {
                break;
            }
        }

        // Award Badges
        $this->awardBadgeIfEligible($user, $streak, 3, '3-Day Streak', '🔥', 'Coded for 3 consecutive days.');
        $this->awardBadgeIfEligible($user, $streak, 7, '7-Day Streak', '🚀', 'Coded for 7 consecutive days.');
        $this->awardBadgeIfEligible($user, $streak, 30, '30-Day Streak', '💎', 'Coded for 30 consecutive days.');
    }

    protected function awardBadgeIfEligible(User $user, int $currentStreak, int $requiredStreak, string $badgeName, string $icon, string $desc)
    {
        if ($currentStreak >= $requiredStreak) {
            UserBadge::firstOrCreate(
                ['user_id' => $user->id, 'badge_name' => $badgeName],
                ['icon' => $icon, 'description' => $desc, 'earned_at' => now()]
            );
        }
    }

    /**
     * Get heatmap data for the last 365 days.
     */
    public function getHeatmapData(User $user)
    {
        $startDate = Carbon::now()->subDays(365);
        $sessions = CodingSession::where('user_id', $user->id)
            ->where('date', '>=', $startDate)
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        $heatmap = [];
        for ($i = 365; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = isset($sessions[$date]) ? $sessions[$date]->commits_count : 0;
            $heatmap[] = [
                'date' => $date,
                'count' => $count,
                'level' => $this->getHeatmapLevel($count)
            ];
        }

        return $heatmap;
    }

    protected function getHeatmapLevel(int $count): int
    {
        if ($count === 0) return 0;
        if ($count <= 2) return 1;
        if ($count <= 5) return 2;
        if ($count <= 10) return 3;
        return 4;
    }
}
