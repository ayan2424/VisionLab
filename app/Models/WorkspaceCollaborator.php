<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WorkspaceCollaborator — Tracks who has access to a workspace beyond the owner.
 * Unique constraint: (workspace_id, user_id)
 */
class WorkspaceCollaborator extends Model
{
    protected $fillable = ['workspace_id', 'user_id', 'role', 'joined_at'];

    protected function casts(): array
    {
        return ['joined_at' => 'datetime'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function canWrite(): bool
    {
        return in_array($this->role, ['owner', 'collaborator'], true);
    }
}
