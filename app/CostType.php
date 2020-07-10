<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostType extends Model
{
    public function grantedClaim()
    {
        return $this->hasOne(GrantedClaim::class);
    }
}
