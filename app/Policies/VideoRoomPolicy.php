<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class VideoRoomPolicy
{
    /** Instructors and admins can start video sessions. */
    public function start(User $user): bool
    {
        return $user->isInstructor() || $user->isAdmin();
    }

    /** Any active workspace collaborator can join. */
    public function join(User $user): bool
    {
        return $user->isActive();
    }

    /** Only instructor who started or admin can end the call. */
    public function end(User $user, Room $room): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $room->created_by;
    }
}
