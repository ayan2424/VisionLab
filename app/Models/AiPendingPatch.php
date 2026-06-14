<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AiPendingPatch — Proposed code changes from AI Agent mode.
 *
 * Lifecycle: pending → approved → applied | rejected | rolled_back | expired
 * All file mutations in Agent mode MUST transit through this model
 * for human approval before being written to the workspace filesystem.
 */
class AiPendingPatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'session_id', 'file_path',
        'original_content', 'patched_content', 'diff',
        'original_hash', 'patched_hash',
        'status', 'created_by', 'reviewer_id',
        'reviewed_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'expires_at'  => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AiChatSession::class, 'session_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    // ── Status Checks ───────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isExpired(): bool
    {
        return $this->isPending()
            && $this->expires_at
            && $this->expires_at->isPast();
    }

    public function canBeReviewed(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }
}
