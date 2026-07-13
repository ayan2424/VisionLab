<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingRubric extends Model
{
    protected $fillable = ['assignment_id', 'criteria', 'max_points', 'description'];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

}
