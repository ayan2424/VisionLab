<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * UserBadge — Gamification badge awarded from real platform events.
 * Unique constraint: (user_id, badge_type) — prevents duplicate awards.
 */
class UserBadge extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'badge_type', 'name', 'description',
        'icon', 'earned_at', 'source_event',
    ];

    protected function casts(): array
    {
        return ['earned_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Award a badge to a user only if they don't already have it.
     */
    public static function awardOnce(int $userId, string $badgeType, string $name, ?string $description = null, ?string $icon = null, ?string $event = null): ?self
    {
        return static::firstOrCreate(
            ['user_id' => $userId, 'badge_type' => $badgeType],
            [
                'name'         => $name,
                'description'  => $description,
                'icon'         => $icon ?? '🏆',
                'earned_at'    => now(),
                'source_event' => $event,
            ]
        );
    }
}
