<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar_url',
        'theme_preference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Role Helpers ──────────────────────────────────────────────

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

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles, true);
    }

    // ── Relationships ────────────────────────────────────────────

    /**
     * Courses this user teaches (as instructor).
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Courses this user is enrolled in (as student).
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'student_id', 'course_id')
            ->withPivot('status', 'enrolled_at')
            ->wherePivot('status', 'active');
    }

    /**
     * All enrollments for this student.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    /**
     * All submissions by this student.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    /**
     * Rooms owned by this user.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'owner_id');
    }

    /**
     * Announcements authored by this user.
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    /**
     * AI chat sessions for this user.
     */
    public function aiChatSessions(): HasMany
    {
        return $this->hasMany(AiChatSession::class);
    }

    /**
     * AI action logs for this user.
     */
    public function aiActionsLog(): HasMany
    {
        return $this->hasMany(AiActionsLog::class);
    }

    // ── Accessors ────────────────────────────────────────────────

    public function getAvatarInitialsAttribute(): string
    {
        $parts = explode(' ', trim($this->name));
        $initials = strtoupper($parts[0][0] ?? '?');
        if (isset($parts[1][0])) {
            $initials .= strtoupper($parts[1][0]);
        }

        return $initials;
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'text-red-400 bg-red-400/10 border-red-400/20',
            'instructor' => 'text-violet-400 bg-violet-400/10 border-violet-400/20',
            'student' => 'text-cyan-400 bg-cyan-400/10 border-cyan-400/20',
            default => 'text-slate-400 bg-slate-400/10 border-slate-400/20',
        };
    }
}
