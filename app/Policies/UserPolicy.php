<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** Only admin can view user list. */
    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    /** Users can view their own profile; admins can view anyone. */
    public function view(User $actor, User $subject): bool
    {
        return $actor->isAdmin() || $actor->id === $subject->id;
    }

    /** Users can update their own profile; admins can update anyone. */
    public function update(User $actor, User $subject): bool
    {
        return $actor->isAdmin() || $actor->id === $subject->id;
    }

    /** Only admin can change roles. */
    public function changeRole(User $actor, User $subject): bool
    {
        return $actor->isAdmin();
    }

    /** Only admin can suspend accounts. Prevents last admin self-suspension. */
    public function suspend(User $actor, User $subject): bool
    {
        if (! $actor->isAdmin()) {
            return false;
        }
        // Prevent suspending the last admin
        if ($subject->isAdmin()) {
            $adminCount = User::where('role', 'admin')
                ->where('status', 'active')
                ->count();
            return $adminCount > 1;
        }
        return true;
    }

    /** Only admin can delete accounts. */
    public function delete(User $actor, User $subject): bool
    {
        if (! $actor->isAdmin()) {
            return false;
        }
        // Prevent deleting the last admin
        if ($subject->isAdmin()) {
            $adminCount = User::where('role', 'admin')
                ->where('status', 'active')
                ->count();
            return $adminCount > 1;
        }
        return true;
    }
}
