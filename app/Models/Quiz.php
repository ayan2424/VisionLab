<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['course_module_id', 'title', 'time_limit_minutes', 'passing_score', 'is_active'];

    //
}
