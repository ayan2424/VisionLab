<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['transaction_id', 'type', 'amount', 'description', 'fee_challan_id'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function feeChallan()
    {
        return $this->belongsTo(FeeChallan::class);
    }

}
