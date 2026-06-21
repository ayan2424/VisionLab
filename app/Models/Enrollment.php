<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'student_id', 'status', 'enrolled_at', 'batch_timing', 'start_date'];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'start_date' => 'date',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
