<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['user_id', 'department_id', 'designation', 'base_salary', 'hire_date', 'is_active'];

    //
}
