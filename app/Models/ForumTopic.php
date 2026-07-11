<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    protected $fillable = ['course_id', 'user_id', 'title', 'body', 'is_pinned'];

    //
}
