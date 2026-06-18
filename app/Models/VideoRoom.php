<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoRoom extends Model
{
    protected $fillable = [
        'course_id',
        'workspace_id',
        'host_id',
        'title',
        'room_name',
        'started_at',
        'ended_at',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }
}
