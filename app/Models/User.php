<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status', 'student_id',
        'avatar_url', 'theme_preference',
        'campus_id', 'department_id', 'phone', 'date_of_birth', 'address', 'guardian_name', 'guardian_phone',
        'last_activity_at', 'current_streak', 'longest_streak',
        'xp', 'level', 'rank_title',
        'vercel_token', 'railway_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'vercel_token',
        'railway_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'last_activity_at'  => 'datetime',
            'current_streak'    => 'integer',
            'longest_streak'    => 'integer',
            'xp'                => 'integer',
            'level'             => 'integer',
            'vercel_token'      => 'encrypted',
            'railway_token'     => 'encrypted',
        ];
    }

    // ── Role Checks ─────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isSubscriber(): bool
    {
        return $this->role === 'subscriber';
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles, true);
    }

    // ── Status Checks ───────────────────────────────────────────────

    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // ── Relationships: Courses ──────────────────────────────────────

    public function taughtCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    // ── Relationships: Classroom ────────────────────────────────────

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    // ── Relationships: Workspace ────────────────────────────────────

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'student_id');
    }


    // ── Relationships: AI ───────────────────────────────────────────

    public function aiSessions(): HasMany
    {
        return $this->hasMany(AiChatSession::class);
    }


    // ── Relationships: Notifications / Prefs ────────────────────────

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function notificationPreference(): HasOne
    {
        return $this->hasOne(NotificationPreference::class);
    }

    // ── Relationships: Gamification ─────────────────────────────────

    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    public function xpTransactions(): HasMany
    {
        return $this->hasMany(XpTransaction::class);
    }

    // ── Gamification Logic ──────────────────────────────────────────

    public function addXp(int $amount, string $reason, ?Model $source = null): void
    {
        if ($amount === 0) return;

        // Record the transaction
        $this->xpTransactions()->create([
            'amount'      => $amount,
            'reason'      => $reason,
            'source_type' => $source ? get_class($source) : null,
            'source_id'   => $source ? $source->id : null,
        ]);

        // Add XP
        $this->xp += $amount;
        
        // Ensure XP never goes below 0
        if ($this->xp < 0) {
            $this->xp = 0;
        }

        $this->recalculateLevelAndRank();
        $this->save();
    }

    public function recalculateLevelAndRank(): void
    {
        // Simple quadratic curve: Level = floor(sqrt(XP / 100)) + 1
        $this->level = floor(sqrt($this->xp / 100)) + 1;

        $rank = 'Novice';
        if ($this->level >= 75) $rank = 'Grandmaster';
        elseif ($this->level >= 50) $rank = 'Principal';
        elseif ($this->level >= 40) $rank = 'Architect';
        elseif ($this->level >= 30) $rank = 'Lead Developer';
        elseif ($this->level >= 20) $rank = 'Senior Developer';
        elseif ($this->level >= 13) $rank = 'Developer';
        elseif ($this->level >= 8) $rank = 'Coder';
        elseif ($this->level >= 4) $rank = 'Apprentice';

        $this->rank_title = $rank;
    }

    // ── Relationships: Audit ────────────────────────────────────────

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    public function latestSnapshot(): HasOne
    {
        return $this->hasOne(AiSnapshot::class)->latestOfMany();
    }

    public function campus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // ── Attributes ──────────────────────────────────────────────────

    public function getAvatarInitialsAttribute(): string
    {
        $parts   = explode(' ', trim($this->name));
        $initials = strtoupper($parts[0][0] ?? '?');
        if (isset($parts[1][0])) {
            $initials .= strtoupper($parts[1][0]);
        }
        return $initials;
    }

    // ── Activity Tracking ───────────────────────────────────────────

    public function touchActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }
}
