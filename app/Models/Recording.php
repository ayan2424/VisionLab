<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Recording extends Model
{
    protected $fillable = [
        'room_slug', 'user_id', 'approved_by', 'status',
        'storage_path', 'playback_url', 'started_at', 'ended_at',
        'duration_seconds', 'approved_at', 'rejection_reason', 'metadata',
    ];

    protected $casts = [
        'started_at'  => 'datetime',
        'ended_at'    => 'datetime',
        'approved_at' => 'datetime',
        'metadata'    => 'array',
    ];

    public function user(): BelongsTo   
    { 
        return $this->belongsTo(User::class); 
    }
    
    public function approver(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'approved_by'); 
    }
    
    public function auditLogs(): HasMany 
    { 
        return $this->hasMany(RecordingAuditLog::class); 
    }

    public function generateSignedUrl(int $expiryMinutes = 60): string
    {
        return Storage::temporaryUrl($this->storage_path, now()->addMinutes($expiryMinutes));
    }

    public function isAccessibleBy(User $user): bool
    {
        if ($user->isAdmin() || $user->id === $this->user_id) return true;
        return $this->status === 'approved';
    }
}