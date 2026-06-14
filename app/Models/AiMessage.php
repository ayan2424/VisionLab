<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AiMessage — Individual message in an AI chat session.
 * Tracks role, content, tool usage, token count, and safety flags.
 */
class AiMessage extends Model
{
    protected $fillable = [
        'session_id', 'role', 'content', 'token_count',
        'tool_name', 'tool_input', 'tool_output', 'safety_flags',
    ];

    protected function casts(): array
    {
        return [
            'token_count'  => 'integer',
            'tool_input'   => 'array',
            'tool_output'  => 'array',
            'safety_flags' => 'array',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AiChatSession::class, 'session_id');
    }

    public function isUserMessage(): bool
    {
        return $this->role === 'user';
    }

    public function isAssistantMessage(): bool
    {
        return $this->role === 'assistant';
    }

    public function isToolCall(): bool
    {
        return $this->tool_name !== null;
    }
}
