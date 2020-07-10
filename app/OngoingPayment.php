<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OngoingPayment extends GrantedClaim
{
    protected $fillable = [
        'claim_id', 'favoredFundsCenter_id', 'chargedFundsCenter_id', 'costType_id', 'grantedFunds', 'plannedStartDate', 'plannedEndDate', 'description', 'due', 'requirements'
    ];

    public function ongoingPaymentHistories()
    {
        return $this->hasMany(OngoingPaymentHistory::class);
    }

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }
}
