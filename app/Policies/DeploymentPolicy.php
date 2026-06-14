<?php

namespace App\Policies;

use App\Models\Deployment;
use App\Models\User;

class DeploymentPolicy
{
    /** Only workspace owner can deploy. */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    /** Owner, course instructor, and admin can view deployment. */
    public function view(User $user, Deployment $deployment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->id === $deployment->user_id) {
            return true;
        }
        return $user->isInstructor()
            && $deployment->workspace->course->instructor_id === $user->id;
    }

    /** Only admin can cancel deployments. */
    public function cancel(User $user, Deployment $deployment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $deployment->user_id && $deployment->isInProgress();
    }
}
