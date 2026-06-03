<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->isAdmin() || $user->id === $course->instructor_id) {
            return true;
        }

        return $course->isEnrolled($user);
    }

    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() || $user->id === $course->instructor_id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin() || $user->id === $course->instructor_id;
    }
}
