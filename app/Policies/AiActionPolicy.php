<?php

namespace App\Policies;

use App\Models\AiPendingPatch;
use App\Models\User;

class AiActionPolicy
{
    /** Any authenticated user can chat with AI. */
    public function chat(User $user): bool
    {
        return $user->isActive();
    }

    /** Any authenticated user can use plan mode. */
    public function plan(User $user): bool
    {
        return $user->isActive();
    }

    /** Students can use agent mode (creates pending patches). */
    public function agent(User $user): bool
    {
        return $user->isActive();
    }

    /** Only workspace owner can approve a patch. */
    public function approvePatch(User $user, AiPendingPatch $patch): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $patch->workspace->isOwnedBy($user);
    }

    /** Same as approve — only workspace participants can reject. */
    public function rejectPatch(User $user, AiPendingPatch $patch): bool
    {
        return $this->approvePatch($user, $patch);
    }

    /** Only workspace participants can rollback. */
    public function rollback(User $user, AiPendingPatch $patch): bool
    {
        return $this->approvePatch($user, $patch);
    }

    /** Admins and instructors can view AI audit logs. */
    public function viewAuditLogs(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }
}
