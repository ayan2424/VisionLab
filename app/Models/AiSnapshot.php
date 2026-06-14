<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AiSnapshot — File content snapshot taken before AI patch application.
 * Enables rollback of approved patches.
 */
class AiSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id', 'session_id', 'file_path',
        'content', 'content_hash', 'created_by',
    ];

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
}
