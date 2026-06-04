<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoRoom extends Model
{
    protected $fillable = [
        'room_id', 'jitsi_room_name', 'jitsi_domain',
        'started_by', 'started_at', 'ended_at', 'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function starter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
