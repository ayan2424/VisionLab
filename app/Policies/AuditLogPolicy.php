<?php

namespace App\Policies;

use App\Models\User;

class AuditLogPolicy
{
    /** Only admins can view audit logs. */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Only admins can search audit logs. */
    public function search(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Only admins can export audit logs. */
    public function export(User $user): bool
    {
        return $user->isAdmin();
    }
}
