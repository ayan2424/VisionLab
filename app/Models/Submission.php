<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id', 'student_id', 'workspace_snapshot_path',
        'code_snapshot', 'status', 'submitted_at', 'grade', 'feedback', 'graded_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'grade' => 'integer',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isLate(): bool
    {
        if (! $this->submitted_at || ! $this->assignment->due_date) {
            return false;
        }

        return $this->submitted_at->gt($this->assignment->due_date);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'graded' => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
            'submitted' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
            'in_progress' => 'text-amber-400 bg-amber-400/10 border-amber-400/20',
            'late' => 'text-red-400 bg-red-400/10 border-red-400/20',
            default => 'text-slate-400 bg-slate-400/10 border-slate-400/20',
        };
    }
}
