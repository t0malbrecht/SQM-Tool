<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OngoingPaymentHistory extends Model
{
    protected $fillable = [
        'ongoing_payment_id', 'grantedFunds', 'spentFunds', 'plannedPaymentDate', 'realPaymentDate'
    ];

    public function ongoingPayment()
    {
        return $this->belongsTo(OngoingPayment::class);
    }
}
