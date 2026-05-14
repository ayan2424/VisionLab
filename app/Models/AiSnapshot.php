<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'workspace_id',
        'file_path',
        'content',
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
