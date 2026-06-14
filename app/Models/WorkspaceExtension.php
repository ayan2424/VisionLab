<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WorkspaceExtension — Tracks which extensions are assigned to a workspace
 * and their sync status. Policy source tracks how the decision was made.
 */
class WorkspaceExtension extends Model
{
    protected $fillable = [
        'workspace_id', 'extension_id', 'is_enabled',
        'policy_source', 'sync_status',
    ];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function extension(): BelongsTo
    {
        return $this->belongsTo(Extension::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', 'pending');
    }
}
