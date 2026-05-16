<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class WorkspacePolicy
{
    public function view(User $user, Room $room): bool
    {
        if ($user->isAdmin()) return true;
        if ($room->owner_id === $user->id) return true;
        return $room->isMember($user);
    }

    public function update(User $user, Room $room): bool
    {
        if ($user->isAdmin()) return true;
        return $room->owner_id === $user->id;
    }

    public function delete(User $user, Room $room): bool
    {
        if ($user->isAdmin()) return true;
        return $room->owner_id === $user->id;
    }

    public function stop(User $user, Room $room): bool
    {
        return $user->isAdmin() || $room->owner_id === $user->id;
    }

    public function manageFiles(User $user, Room $room): bool
    {
        if ($user->isAdmin()) return true;
        if ($room->owner_id === $user->id) return true;
        return $room->isMember($user);
    }
}
