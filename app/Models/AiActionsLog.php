<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AiActionsLog — Structured audit trail for all AI operations.
 * Tracks action type, file paths, content hashes, mode, trigger source, and result.
 */
class AiActionsLog extends Model
{
    protected $table = 'ai_actions_log';

    protected $fillable = [
        'user_id', 'workspace_id', 'workspace_ref', 'session_id',
        'action_type', 'file_path', 'diff_summary',
        'content_hashes', 'trigger_source', 'result', 'mode',
    ];

    protected function casts(): array
    {
        return [
            'content_hashes' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AiChatSession::class, 'session_id');
    }

    public function scopeForWorkspace($query, int $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    public function scopeForAction($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }
}
