<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * NotificationPreference — User notification channel and event settings.
 * Supports quiet hours to suppress non-critical notifications.
 */
class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id', 'channel_prefs', 'event_prefs',
        'quiet_hours_start', 'quiet_hours_end',
    ];

    protected function casts(): array
    {
        return [
            'channel_prefs' => 'array',
            'event_prefs'   => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a specific notification channel is enabled.
     */
    public function isChannelEnabled(string $channel): bool
    {
        $prefs = $this->channel_prefs ?? [];
        return $prefs[$channel] ?? true; // Enabled by default
    }

    /**
     * Check if a specific event type notification is enabled.
     */
    public function isEventEnabled(string $event): bool
    {
        $prefs = $this->event_prefs ?? [];
        return $prefs[$event] ?? true; // Enabled by default
    }

    /**
     * Check if currently within quiet hours.
     */
    public function isInQuietHours(): bool
    {
        if (! $this->quiet_hours_start || ! $this->quiet_hours_end) {
            return false;
        }

        $now   = now()->format('H:i');
        $start = $this->quiet_hours_start;
        $end   = $this->quiet_hours_end;

        // Handle overnight quiet hours (e.g., 22:00–07:00)
        if ($start > $end) {
            return $now >= $start || $now <= $end;
        }

        return $now >= $start && $now <= $end;
    }

    /**
     * Get or create preferences for a user.
     */
    public static function forUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }
}
