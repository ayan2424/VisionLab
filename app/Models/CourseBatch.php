<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBatch extends Model
{
    protected $fillable = [
        'course_id', 'department_id', 'semester_id', 'instructor_id',
        'title', 'timing', 'start_date', 'max_capacity', 'room_number', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'batch_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'batch_id');
    }
}
