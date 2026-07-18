<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBadge;
use App\Models\AnalyticsEvent;
use Illuminate\Database\Eloquent\Model;

class GamificationService
{
    /**
     * Evaluate a user's stats and award any unlocked badges.
     */
    public static function evaluateBadges(User $user): void
    {
        // 1. XP Threshold Badges
        if ($user->xp >= 1000) {
            UserBadge::awardOnce($user->id, 'xp_1k', 'Rising Star', 'Reached 1,000 XP', '🌟', 'xp_milestone');
        }
        if ($user->xp >= 5000) {
            UserBadge::awardOnce($user->id, 'xp_5k', 'Dedicated Coder', 'Reached 5,000 XP', '🔥', 'xp_milestone');
        }
        if ($user->xp >= 10000) {
            UserBadge::awardOnce($user->id, 'xp_10k', 'Visionary', 'Reached 10,000 XP', '👁️', 'xp_milestone');
        }

        // 2. Streak Badges
        if ($user->longest_streak >= 3) {
            UserBadge::awardOnce($user->id, 'streak_3', 'Warm Up', 'Logged in 3 days in a row', '📅', 'streak_milestone');
        }
        if ($user->longest_streak >= 7) {
            UserBadge::awardOnce($user->id, 'streak_7', 'Week Warrior', 'Logged in 7 days in a row', '🗓️', 'streak_milestone');
        }
        if ($user->longest_streak >= 30) {
            UserBadge::awardOnce($user->id, 'streak_30', 'Unstoppable', 'Logged in 30 days in a row', '🏃', 'streak_milestone');
        }

        // 3. Workspace Usage Badges
        $workspaceCount = AnalyticsEvent::where('user_id', $user->id)->where('event_type', 'workspace_started')->count();
        if ($workspaceCount >= 10) {
            UserBadge::awardOnce($user->id, 'workspace_10', 'Environment Explorer', 'Started 10 workspaces', '💻', 'workspace_usage');
        }
        if ($workspaceCount >= 50) {
            UserBadge::awardOnce($user->id, 'workspace_50', 'Docker Master', 'Started 50 workspaces', '🐳', 'workspace_usage');
        }

        // 4. Assignment Badges
        if (class_exists(\App\Models\Submission::class)) {
            $submissionCount = \App\Models\Submission::where('student_id', $user->id)->whereIn('status', ['submitted', 'late', 'graded'])->count();
            if ($submissionCount >= 1) {
                UserBadge::awardOnce($user->id, 'first_submission', 'First Steps', 'Completed your first assignment', '📝', 'assignment');
            }
            if ($submissionCount >= 10) {
                UserBadge::awardOnce($user->id, 'submission_10', 'Homework Hero', 'Completed 10 assignments', '📚', 'assignment');
            }
            
            $perfectScores = \App\Models\Submission::where('student_id', $user->id)->where('status', 'graded')->where('grade', '>=', 100)->count();
            if ($perfectScores >= 1) {
                UserBadge::awardOnce($user->id, 'perfect_score_1', 'Perfectionist', 'Got a 100% on an assignment', '💯', 'assignment');
            }
        }

        // 5. Tests / Quizzes Badges
        if (class_exists(\App\Models\QuizAttempt::class)) {
            $quizCount = \App\Models\QuizAttempt::where('user_id', $user->id)->count();
            if ($quizCount >= 1) {
                UserBadge::awardOnce($user->id, 'quiz_1', 'Test Taker', 'Completed your first quiz', '✅', 'quiz');
            }
            if ($quizCount >= 5) {
                UserBadge::awardOnce($user->id, 'quiz_5', 'Quiz Master', 'Completed 5 quizzes', '🧠', 'quiz');
            }
        }

        // 6. Attendance / Classes
        if (class_exists(\App\Models\AttendanceLog::class)) {
            $attendanceCount = \App\Models\AttendanceLog::where('student_id', $user->id)->where('status', 'present')->count();
            if ($attendanceCount >= 10) {
                UserBadge::awardOnce($user->id, 'attendance_10', 'Punctual', 'Attended 10 classes', '🕒', 'attendance');
            }
            if ($attendanceCount >= 30) {
                UserBadge::awardOnce($user->id, 'attendance_30', 'Always Present', 'Attended 30 classes', '🏆', 'attendance');
            }
        }
    }

    /**
     * Helper to award XP and evaluate badges in one go.
     */
    public static function awardXpAndEvaluate(User $user, int $amount, string $reason, ?Model $source = null): void
    {
        $user->addXp($amount, $reason, $source);
        self::evaluateBadges($user);
    }
}
