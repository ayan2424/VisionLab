<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Extension — VS Code extension registry entry.
 * Tracks artifacts, build provenance, policy enforcement, and rollout state.
 */
class Extension extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'package_identifier', 'version', 'description',
        'category', 'artifact_path', 'checksum', 'source',
        'is_global', 'is_builtin', 'is_required', 'is_active',
        'rollout_state',
    ];

    protected $casts = [
        'is_global'   => 'boolean',
        'is_builtin'  => 'boolean',
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function builds(): HasMany
    {
        return $this->hasMany(ExtensionBuild::class);
    }

    public function workspaceExtensions(): HasMany
    {
        return $this->hasMany(WorkspaceExtension::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeBuiltin($query)
    {
        return $query->where('is_builtin', true);
    }

    public function scopeReleased($query)
    {
        return $query->where('rollout_state', 'released');
    }

    public function isImmutable(): bool
    {
        return $this->is_required || $this->is_builtin;
    }

    public function latestBuild(): ?ExtensionBuild
    {
        return $this->builds()->latest()->first();
    }
}
