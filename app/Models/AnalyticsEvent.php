<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * AnalyticsEvent — Core telemetry event model for VisionLab.
 * Every user action of significance is recorded here for dashboards,
 * VisionGuard forensics, gamification triggers, and audit compliance.
 */
class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_type', 'event_data',
        'resource_type', 'resource_id',
        'ip_address', 'user_agent', 'correlation_id',
    ];

    protected function casts(): array
    {
        return [
            'event_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    public function scopeForResource($query, string $type, ?int $id = null)
    {
        return $query->where('resource_type', $type)
            ->when($id, fn ($q) => $q->where('resource_id', $id));
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ── Static Logger ───────────────────────────────────────────────

    /**
     * Record an analytics event with automatic request metadata capture.
     */
    public static function track(
        string $eventType,
        array $data = [],
        ?int $userId = null,
        ?string $resourceType = null,
        ?int $resourceId = null,
        ?string $correlationId = null,
    ): self {
        return static::create([
            'user_id'        => $userId ?? auth()->id(),
            'event_type'     => $eventType,
            'event_data'     => $data,
            'resource_type'  => $resourceType,
            'resource_id'    => $resourceId,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'correlation_id' => $correlationId ?? Str::uuid()->toString(),
        ]);
    }
}
