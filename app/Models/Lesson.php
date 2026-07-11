<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['course_module_id', 'title', 'type', 'content_url', 'body', 'duration_minutes', 'order_index'];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }
}
