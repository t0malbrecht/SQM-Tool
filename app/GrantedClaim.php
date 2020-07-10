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
        return $this->hasMany(ProofOfUse::class);
    }
}
