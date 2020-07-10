<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OneTimePayment extends GrantedClaim
{
    protected $fillable = [
        'claim_id', 'favoredFundsCenter_id', 'chargedFundsCenter_id', 'grantedFunds', 'spentFunds', 'spentDate', 'description', 'costType_id', 'requirements', 'dueDate',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }

}
