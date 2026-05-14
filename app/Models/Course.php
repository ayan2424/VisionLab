<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'instructor_id', 'title', 'slug', 'description',
        'cover_image', 'enrollment_code', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title) . '-' . Str::random(4);
            }
            if (empty($course->enrollment_code)) {
                $course->enrollment_code = strtoupper(Str::random(6));
            }
        });
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
                    ->withPivot('status', 'enrolled_at')
                    ->wherePivot('status', 'active');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class)->orderBy('due_date');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class)->latest();
    }

    public function isEnrolled(User $user): bool
    {
        return $this->enrollments()
                    ->where('student_id', $user->id)
                    ->where('status', 'active')
                    ->exists();
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getCoverGradientAttribute(): string
    {
        $gradients = [
            'from-violet-600 to-indigo-800',
            'from-cyan-600 to-blue-800',
            'from-emerald-600 to-teal-800',
            'from-rose-600 to-pink-800',
            'from-amber-600 to-orange-800',
            'from-purple-600 to-violet-800',
        ];
        return $gradients[$this->id % count($gradients)];
    }
}
