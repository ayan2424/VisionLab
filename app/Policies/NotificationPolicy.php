<?php

namespace App\Policies;

use App\Models\PushSubscription;
use App\Models\User;

class NotificationPolicy
{
    /** Users can manage their own push subscriptions. */
    public function subscribe(User $user): bool
    {
        return $user->isActive();
    }

    /** Users can only unsubscribe their own subscriptions. */
    public function unsubscribe(User $user, PushSubscription $subscription): bool
    {
        return $user->id === $subscription->user_id;
    }

    /** Users can only manage their own notification preferences. */
    public function managePreferences(User $user): bool
    {
        return $user->isActive();
    }
}
