<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SqmPayment extends Model
{
    protected $fillable = [
        'startDate', 'dueDate', 'amount', 'fundsCenter_id',
    ];

    public function fundscenter()
    {
        return $this->belongsTo(FundsCenter::class, 'fundsCenter_id');
    }
}
