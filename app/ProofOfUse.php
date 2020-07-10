<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProofOfUse extends Model
{
    public function oneTimePayment()
    {
        return $this->belongsTo(GrantedClaim::class);
    }
}
