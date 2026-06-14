<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * WorkspaceQuota — Defines resource limits for workspace containers.
 *
 * Resolution order: user-specific → course-specific → global → hard fallback.
 * Scopes: global (scope_id=null), course (scope_id=course_id), user (scope_id=user_id).
 */
class WorkspaceQuota extends Model
{
    protected $fillable = [
        'name', 'memory_mb', 'cpu_shares', 'disk_mb',
        'timeout_minutes', 'scope', 'scope_id', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'memory_mb'       => 'integer',
            'cpu_shares'      => 'integer',
            'disk_mb'         => 'integer',
            'timeout_minutes' => 'integer',
            'is_active'       => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForScope($query, string $scope, ?int $scopeId = null)
    {
        return $query->where('scope', $scope)
            ->when($scopeId, fn ($q) => $q->where('scope_id', $scopeId));
    }

    public function scopeGlobal($query)
    {
        return $query->where('scope', 'global');
    }

    /**
     * Resolve the effective quota for a given user and course.
     * Resolution: user → course → global → hard fallback.
     */
    public static function resolveFor(?int $userId = null, ?int $courseId = null): self
    {
        if ($userId) {
            $userQuota = static::active()->forScope('user', $userId)->first();
            if ($userQuota) {
                return $userQuota;
            }
        }

        if ($courseId) {
            $courseQuota = static::active()->forScope('course', $courseId)->first();
            if ($courseQuota) {
                return $courseQuota;
            }
        }

        $globalQuota = static::active()->global()->first();
        if ($globalQuota) {
            return $globalQuota;
        }

        // Hard platform fallback — no database record needed
        $fallback = new static();
        $fallback->name = 'Platform Default';
        $fallback->memory_mb = 512;
        $fallback->cpu_shares = 1024;
        $fallback->disk_mb = 1024;
        $fallback->timeout_minutes = 120;
        $fallback->scope = 'global';
        $fallback->is_active = true;

        return $fallback;
    }
}
