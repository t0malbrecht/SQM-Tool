<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrantedClaim extends Model
{
    public function favoredFundsCenter()
    {
        return $this->belongsTo(FundsCenter::class, 'favoredFundsCenter_id');
    }

    public function chargedFundsCenter()
    {
        return $this->belongsTo(FundsCenter::class, 'chargedFundsCenter_id');
    }

    public function costType()
    {
        return $this->belongsTo(CostType::class, 'costType_id');
    }

    public function proofOfUse()
    {
        return $this->hasOne(ProofOfUse::class, 'grantedClaim_id', 'unique_payment_id');
    }

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }
}
