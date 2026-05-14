<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiPendingPatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'session_id',
        'file_path',
        'original_content',
        'patched_content',
        'diff',
        'status',
        'created_by',
    ];

    public function workspace()
    {
        return $this->belongsTo(Room::class, 'workspace_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
