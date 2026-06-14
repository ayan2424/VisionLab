<?php

namespace App\Policies;

use App\Models\User;

class AnalyticsPolicy
{
    /** Students see only their own analytics. */
    public function viewOwn(User $user): bool
    {
        return true;
    }

    /** Instructors see analytics for their own courses. */
    public function viewCourseAnalytics(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /** Only admin sees platform-wide analytics. */
    public function viewPlatformAnalytics(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Only admin and instructor can view VisionGuard forensics. */
    public function viewForensics(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }
}
