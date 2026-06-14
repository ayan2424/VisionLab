<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;

class EnrollmentPolicy
{
    /** Students can join courses they aren't already enrolled in. */
    public function join(User $user, Course $course): bool
    {
        if (! $user->isStudent()) {
            return false;
        }
        return ! $course->isEnrolled($user);
    }

    /** Instructors can remove students from their own courses; admins can always. */
    public function remove(User $user, Enrollment $enrollment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->isInstructor()
            && $enrollment->course->instructor_id === $user->id;
    }

    /** Students can leave courses they are enrolled in. */
    public function leave(User $user, Enrollment $enrollment): bool
    {
        return $user->id === $enrollment->student_id;
    }
}
