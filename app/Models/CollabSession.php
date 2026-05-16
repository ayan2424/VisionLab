<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollabSession extends Model
{
    protected $fillable = [
        'room_id', 'user_id', 'cursor_file', 'cursor_line',
        'cursor_col', 'cursor_color', 'last_heartbeat',
    ];

    protected $casts = [
        'cursor_line'    => 'integer',
        'cursor_col'     => 'integer',
        'last_heartbeat' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
