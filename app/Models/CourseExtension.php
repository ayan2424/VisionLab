<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseExtension extends Model
{
    protected $fillable = ['course_id', 'extension_id', 'is_required'];

    protected function casts(): array
    {
        return ['is_required' => 'boolean'];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function extension()
    {
        return $this->belongsTo(Extension::class);
    }
}
