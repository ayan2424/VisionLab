<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->id === $assignment->course->instructor_id) {
            return true;
        }

        return $assignment->course->isEnrolled($user);
    }

    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->course->instructor_id;
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->course->instructor_id;
    }

    public function start(User $user, Assignment $assignment): bool
    {
        return $user->isStudent() && $assignment->course->isEnrolled($user);
    }

    public function grade(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->course->instructor_id;
    }
}
