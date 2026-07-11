<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['course_id', 'user_id', 'certificate_code', 'issued_at', 'pdf_url'];

    //
}
