<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id', 'title', 'description', 'max_points',
        'due_date', 'starter_code', 'starter_language', 'auto_workspace',
        'mode', 'allow_ai',
    ];

    protected $casts = [
        'due_date'       => 'datetime',
        'auto_workspace' => 'boolean',
        'max_points'     => 'integer',
        'allow_ai'       => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function submissionFor(User $user): ?Submission
    {
        return $this->submissions()->where('student_id', $user->id)->first();
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function getDueSoonAttribute(): bool
    {
        if (!$this->due_date) return false;
        return $this->due_date->isFuture() && $this->due_date->diffInHours(now()) <= 48;
    }

    public function getTimeRemainingAttribute(): string
    {
        if (!$this->due_date) return 'No due date';
        if ($this->due_date->isPast()) return 'Past due';
        return $this->due_date->diffForHumans(['parts' => 2]);
    }

    public function getPendingSubmissionsCountAttribute(): int
    {
        return $this->submissions()->where('status', 'submitted')->count();
    }
}
