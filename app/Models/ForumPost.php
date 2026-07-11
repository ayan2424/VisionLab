<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    protected $fillable = ['forum_topic_id', 'user_id', 'body'];

    //
}
