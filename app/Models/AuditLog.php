<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * AuditLog — Structured audit trail for all sensitive platform actions.
 *
 * Records actor, action, affected resource, state changes, result,
 * request metadata (IP, user-agent), and a correlation identifier for
 * grouping related events.
 */
class AuditLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'actor_id', 'action', 'resource_type', 'resource_id',
        'old_state', 'new_state', 'result', 'ip_address',
        'user_agent', 'correlation_id',
    ];

    protected function casts(): array
    {
        return [
            'old_state' => 'array',
            'new_state' => 'array',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeForActor($query, int $actorId)
    {
        return $query->where('actor_id', $actorId);
    }

    public function scopeForResource($query, string $type, ?int $id = null)
    {
        return $query->where('resource_type', $type)
            ->when($id, fn ($q) => $q->where('resource_id', $id));
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ── Static Logger ───────────────────────────────────────────────

    /**
     * Record an audit event with automatic request metadata capture.
     */
    public static function record(
        string $action,
        string $resourceType,
        ?int $resourceId = null,
        ?array $oldState = null,
        ?array $newState = null,
        string $result = 'success',
        ?string $correlationId = null,
    ): self {
        return static::create([
            'actor_id'       => auth()->id(),
            'action'         => $action,
            'resource_type'  => $resourceType,
            'resource_id'    => $resourceId,
            'old_state'      => $oldState,
            'new_state'      => $newState,
            'result'         => $result,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
            'correlation_id' => $correlationId ?? Str::uuid()->toString(),
        ]);
    }
}
