<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Deployment — Tracks student project deployment through external providers.
 * Lifecycle: queued → building → deployed / failed / cancelled
 */
class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'user_id', 'provider', 'deployment_id',
        'public_url', 'status', 'job_metadata', 'error_summary',
        'deployed_at', 'notification_sent',
    ];

    protected function casts(): array
    {
        return [
            'job_metadata'      => 'array',
            'deployed_at'       => 'datetime',
            'notification_sent' => 'boolean',
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

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'deployed');
    }

    public function isDeployed(): bool
    {
        return $this->status === 'deployed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, ['queued', 'building'], true);
    }
}
