<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CollabSession — Tracks live presence state for a user inside a workspace.
 * Used for cursor sync, selection sharing, and heartbeat-based cleanup.
 */
class CollabSession extends Model
{
    protected $fillable = [
        'workspace_id', 'user_id', 'cursor_state', 'selection_state',
        'is_online', 'heartbeat_at', 'color',
    ];

    protected function casts(): array
    {
        return [
            'cursor_state'    => 'array',
            'selection_state' => 'array',
            'is_online'       => 'boolean',
            'heartbeat_at'    => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    /**
     * Stale sessions have not sent a heartbeat in the last 2 minutes.
     */
    public function scopeStale($query)
    {
        return $query->where('is_online', true)
            ->where('heartbeat_at', '<', now()->subMinutes(2));
    }

    public function isStale(): bool
    {
        return $this->is_online
            && $this->heartbeat_at
            && $this->heartbeat_at->diffInMinutes(now()) > 2;
    }
}
