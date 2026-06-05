<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordingAuditLog extends Model
{
    protected $fillable = ['recording_id', 'user_id', 'action', 'ip_address', 'user_agent', 'context'];
    protected $casts    = ['context' => 'array'];

    public function recording(): BelongsTo 
    { 
        return $this->belongsTo(Recording::class); 
    }
    
    public function user(): BelongsTo      
    { 
        return $this->belongsTo(User::class); 
    }
}