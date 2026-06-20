<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkspaceTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'git_url',
        'language',
        'start_command',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
