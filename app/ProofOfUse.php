<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProofOfUse extends Model
{
    public function grantedClaim()
    {
        return $this->belongsTo(GrantedClaim::class, 'grantedClaim_id');
    }
}
