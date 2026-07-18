<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /** Only owner or collaborator or course instructor or admin can view a workspace. */
    public function view(User $user, Workspace $workspace): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isInstructor() && $workspace->course && $workspace->course->instructor_id === $user->id) {
            return true;
        }
        return $workspace->isOwnedBy($user);
    }

    /** Owner, collaborator, course instructor, or admin can access a workspace. */
    public function access(User $user, Workspace $workspace): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isInstructor() && $workspace->course && $workspace->course->instructor_id === $user->id) {
            return true;
        }
        return $workspace->isOwnedBy($user);
    }

    /** Only students can create workspaces. */
    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    /** Only owner or admin can start/stop a workspace. */
    public function manage(User $user, Workspace $workspace): bool
    {
        return $user->isAdmin() || $workspace->isOwnedBy($user);
    }

    /** Only owner or admin can write files. */
    public function writeFiles(User $user, Workspace $workspace): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $workspace->isOwnedBy($user);
    }

    public function readFiles(User $user, Workspace $workspace): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isInstructor() && $workspace->course && $workspace->course->instructor_id === $user->id) {
            return true;
        }
        return $workspace->isOwnedBy($user);
    }

    /** Only owner or admin can delete a workspace. */
    public function delete(User $user, Workspace $workspace): bool
    {
        return $user->isAdmin() || $workspace->isOwnedBy($user);
    }

    /** Only admin can force-stop any workspace. */
    public function forceStop(User $user): bool
    {
        return $user->isAdmin();
    }
}
