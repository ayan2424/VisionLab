<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AiChatSession — Represents an AI conversation context within a workspace.
 * Modes: CHAT (read-only), PLAN (read+propose), AGENT (prepare patches).
 */
class AiChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'workspace_id', 'workspace_ref', 'title', 'mode',
        'token_total', 'context_metadata', 'provider_metadata',
    ];

    protected function casts(): array
    {
        return [
            'token_total'       => 'integer',
            'context_metadata'  => 'array',
            'provider_metadata' => 'array',
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

    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'session_id');
    }

    public function pendingPatches(): HasMany
    {
        return $this->hasMany(AiPendingPatch::class, 'session_id');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(AiSnapshot::class, 'session_id');
    }

    public function isChatMode(): bool
    {
        return $this->mode === 'CHAT';
    }

    public function isPlanMode(): bool
    {
        return $this->mode === 'PLAN';
    }

    public function isAgentMode(): bool
    {
        return $this->mode === 'AGENT';
    }

    public function incrementTokens(int $count): void
    {
        $this->increment('token_total', $count);
    }
}
