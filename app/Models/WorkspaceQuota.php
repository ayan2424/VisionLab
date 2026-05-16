<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'memory_limit',
        'cpu_limit',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Resolve the quota for a specific room/user.
     */
    public static function resolveForRoom(Room $room): self
    {
        // Check if there is a specific quota for this user
        $userQuota = static::where('user_id', $room->owner_id)->first();
        if ($userQuota) {
            return $userQuota;
        }

        // Check if there is a quota for the course (assuming we linked room to course via workspace)
        // For MVP, we'll return a default if none exists
        return new self([
            'memory_limit' => '512m',
            'cpu_limit' => '0.5',
        ]);
    }
}
