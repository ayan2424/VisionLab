<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoAttendance extends Model
{
    protected $fillable = [
        'video_room_id',
        'user_id',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function videoRoom()
    {
        return $this->belongsTo(VideoRoom::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
