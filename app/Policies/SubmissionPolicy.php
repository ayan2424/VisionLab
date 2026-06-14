<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    /** Students can view their own submissions; instructors see their course subs. */
    public function view(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->id === $submission->student_id) {
            return true;
        }
        return $user->isInstructor()
            && $submission->assignment->course->instructor_id === $user->id;
    }

    /** Only the submission owner can submit. */
    public function submit(User $user, Submission $submission): bool
    {
        return $user->id === $submission->student_id
            && $submission->status !== 'graded';
    }

    /** Only the course instructor or admin can grade. */
    public function grade(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->isInstructor()
            && $submission->assignment->course->instructor_id === $user->id;
    }
}
