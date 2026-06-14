<?php

namespace App\Policies;

use App\Models\Extension;
use App\Models\User;

class ExtensionPolicy
{
    /** Only admin can manage the extension registry. */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Extension $extension): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Extension $extension): bool
    {
        if ($extension->isImmutable()) {
            return false; // Required/builtin extensions can never be deleted
        }
        return $user->isAdmin();
    }

    /** Students cannot remove required/builtin extensions. */
    public function uninstall(User $user, Extension $extension): bool
    {
        if ($extension->isImmutable()) {
            return false;
        }
        return true;
    }

    /** Only admin can manage marketplace policy. */
    public function manageMarketplace(User $user): bool
    {
        return $user->isAdmin();
    }
}
