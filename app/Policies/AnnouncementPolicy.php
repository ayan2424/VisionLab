<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    /** Only the course instructor or admin can create announcements. */
    public function create(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /** Enrolled students, course instructor, and admins can view. */
    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->isAdmin() || $user->id === $announcement->author_id) {
            return true;
        }
        return $announcement->course->isEnrolled($user);
    }

    /** Only the author or admin can update. */
    public function update(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin() || $user->id === $announcement->author_id;
    }

    /** Author, course instructor, or admin can delete. */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->isAdmin()
            || $user->id === $announcement->author_id
            || $user->id === $announcement->course->instructor_id;
    }
}
