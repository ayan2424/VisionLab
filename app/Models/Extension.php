<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    protected $fillable = [
        'name', 'package_identifier', 'version', 'description',
        'is_global', 'is_builtin', 'is_active',
    ];

    protected $casts = [
        'is_global'  => 'boolean',
        'is_builtin' => 'boolean',
        'is_active'  => 'boolean',
    ];
}
