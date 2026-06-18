<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBadge;

class GamificationService
{
    /**
     * Evaluate user activities and award badges.
     */
    public function evaluateUser(User $user)
    {
        // 1. First Submission Badge
        if ($user->submissions()->count() >= 1) {
            UserBadge::awardOnce(
                $user->id,
                'first_submission',
                'First Blood',
                'Submitted your first assignment.',
                '🎯'
            );
        }

        // 2. 7-Day Streak Badge
        if ($user->current_streak >= 7) {
            UserBadge::awardOnce(
                $user->id,
                'streak_7_days',
                '7 Day Streak',
                'Logged in and contributed for 7 consecutive days.',
                '🔥'
            );
        }

        // 3. 30-Day Streak Badge
        if ($user->current_streak >= 30) {
            UserBadge::awardOnce(
                $user->id,
                'streak_30_days',
                '30 Day Streak',
                'Logged in and contributed for 30 consecutive days.',
                '⚡'
            );
        }

        // 4. Ten Submissions Badge
        if ($user->submissions()->count() >= 10) {
            UserBadge::awardOnce(
                $user->id,
                'ten_submissions',
                'Dedicated Learner',
                'Submitted 10 assignments.',
                '📚'
            );
        }

        // 5. First Workspace
        if ($user->workspaces()->count() >= 1) {
            UserBadge::awardOnce(
                $user->id,
                'first_workspace',
                'Builder',
                'Created your first workspace.',
                '🏗️'
            );
        }
    }
}
