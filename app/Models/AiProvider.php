<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'api_key',
        'api_base',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function aiModels()
    {
        return $this->hasMany(AiModel::class, 'provider_id');
    }
}
