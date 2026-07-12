<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Workspace — Represents a VisionLab IDE container workspace.
 *
 * Lifecycle states: pending → running → stopped | error
 * Each workspace is scoped to a course (and optionally an assignment)
 * and owned by a single student. Collaborators are tracked separately.
 */
class Workspace extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'assignment_id', 'student_id', 'name', 'slug', 'template_id',
        'container_id', 'port', 'token', 'storage_path',
        'heartbeat_at', 'quota_data', 'proxy_url', 'container_image',
        'status', 'language', 'type', 'subscription_id', 'governance_level',
    ];

    protected function casts(): array
    {
        return [
            'quota_data'   => 'array',
            'heartbeat_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workspace) {
            if (empty($workspace->slug)) {
                $userName = $workspace->owner ? $workspace->owner->name : 'user';
                $templateName = $workspace->template ? $workspace->template->name : 'workspace';
                
                $baseSlug = \Illuminate\Support\Str::slug($userName . '-' . $templateName . '-' . $workspace->name);
                
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }
                $workspace->slug = $slug;
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // ── Relationships ───────────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkspaceTemplate::class, 'template_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(WorkspaceCollaborator::class);
    }

    public function collabSessions(): HasMany
    {
        return $this->hasMany(CollabSession::class);
    }

    public function aiSessions(): HasMany
    {
        return $this->hasMany(AiChatSession::class);
    }

    public function pendingPatches(): HasMany
    {
        return $this->hasMany(AiPendingPatch::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(AiSnapshot::class);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(WorkspaceExtension::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    public function forensics(): HasOne
    {
        return $this->hasOne(SubmissionForensic::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('student_id', $user->id);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'running']);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isGoverned(): bool
    {
        return $this->type === 'governed';
    }

    public function isIndependent(): bool
    {
        return $this->type === 'independent';
    }

    public function isStopped(): bool
    {
        return $this->status === 'stopped';
    }

    public function isHealthy(): bool
    {
        if (! $this->isRunning()) {
            return false;
        }
        // A workspace is healthy if it has heartbeat within last 5 minutes
        return $this->heartbeat_at && $this->heartbeat_at->diffInMinutes(now()) < 5;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->student_id === $user->id;
    }

    public function hasCollaborator(User $user): bool
    {
        return $this->isOwnedBy($user)
            || $this->collaborators()->where('user_id', $user->id)->exists();
    }
}
