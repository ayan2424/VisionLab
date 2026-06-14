<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    /** Only instructors/admins can create assignments. */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /** Enrolled students, course instructor, and admins can view. */
    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->isAdmin() || $user->id === $assignment->course->instructor_id) {
            return true;
        }
        return $assignment->course->isEnrolled($user);
    }

    /** Only the course instructor or admin can update. */
    public function update(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->course->instructor_id;
    }

    /** Only the course instructor or admin can delete. */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->isAdmin() || $user->id === $assignment->course->instructor_id;
    }

    /** Enrolled students can start an assignment. */
    public function start(User $user, Assignment $assignment): bool
    {
        return $user->isStudent() && $assignment->course->isEnrolled($user);
    }
}
