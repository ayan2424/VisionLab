<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryBook extends Model
{
    protected $fillable = ['isbn', 'title', 'author', 'total_copies', 'available_copies'];

    //
}
