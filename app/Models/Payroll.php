<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = ['employee_id', 'month_year', 'basic_pay', 'allowances', 'deductions', 'net_pay', 'status', 'payment_date'];

    //
}
