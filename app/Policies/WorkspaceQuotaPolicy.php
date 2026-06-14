<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkspaceQuota;

class WorkspaceQuotaPolicy
{
    /** Only admin can view quotas. */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Only admin can create quotas. */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /** Only admin can update quotas. */
    public function update(User $user, WorkspaceQuota $quota): bool
    {
        return $user->isAdmin();
    }

    /** Only admin can delete quotas. */
    public function delete(User $user, WorkspaceQuota $quota): bool
    {
        return $user->isAdmin();
    }
}
