<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeChallan extends Model
{
    protected $fillable = ['user_id', 'challan_number', 'amount', 'late_fee', 'due_date', 'status', 'paid_date'];

    //
}
