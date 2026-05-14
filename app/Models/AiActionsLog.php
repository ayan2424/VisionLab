<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiActionsLog extends Model
{
    protected $table = 'ai_actions_log';

    protected $fillable = [
        'user_id', 'workspace_ref', 'action_type', 'file_path', 'diff_summary', 'mode',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
