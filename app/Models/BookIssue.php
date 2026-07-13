<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    protected $fillable = ['library_book_id', 'user_id', 'issue_date', 'due_date', 'return_date', 'status', 'fine_amount'];

    public function libraryBook()
    {
        return $this->belongsTo(LibraryBook::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
