<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function view(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->id === $submission->student_id) return true;

        $course = $submission->assignment->course;
        return $user->id === $course->instructor_id;
    }

    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    public function grade(User $user, Submission $submission): bool
    {
        if ($user->isAdmin()) return true;
        return $user->id === $submission->assignment->course->instructor_id;
    }

    public function submit(User $user, Submission $submission): bool
    {
        return $user->id === $submission->student_id && $submission->status !== 'graded';
    }
}
