<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * PushSubscription — Stores VAPID Web Push subscription data.
 * Each subscription is bound to an authenticated user.
 */
class PushSubscription extends Model
{
    protected $fillable = [
        'user_id', 'endpoint', 'p256dh_key', 'auth_token',
        'content_encoding', 'browser_info', 'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'browser_info' => 'array',
            'revoked_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function revoke(): void
    {
        $this->update(['revoked_at' => now()]);
    }
}
