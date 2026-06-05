<?php

namespace App\Policies;

use App\Models\Recording;
use App\Models\User;

class RecordingPolicy
{
    public function viewAny(User $user): bool 
    { 
        return $user->isAdmin(); 
    }
    
    public function view(User $user, Recording $recording): bool
    {
        return $recording->isAccessibleBy($user);
    }
    
    public function approve(User $user, Recording $recording): bool
    {
        return $user->isAdmin();
    }
    
    public function manage(User $user, Recording $recording): bool
    {
        return $user->isAdmin() || $user->id === $recording->user_id;
    }
}