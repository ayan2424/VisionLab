<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Extension extends Model
{
    protected $fillable = [
        'name', 'package_identifier', 'version', 'description',
        'is_global', 'is_builtin', 'is_active',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'is_builtin' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Workspaces this extension is enabled/disabled for.
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'workspace_extensions', 'extension_id', 'room_id')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    /**
     * Check if extension is enabled for a specific workspace.
     */
    public function isEnabledFor(Room $room): bool
    {
        if ($this->is_global) {
            return true;
        }

        $pivot = $this->workspaces()->where('room_id', $room->id)->first();

        return $pivot ? $pivot->pivot->is_enabled : false;
    }
}
