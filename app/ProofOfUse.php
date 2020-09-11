<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProofOfUse extends Model
{
    protected $fillable = [
        'grantedClaim_id', 'submitter', 'submitDate', 'document'
    ];

    public function grantedClaim()
    {
        return $this->belongsTo(OneTimePayment::class, 'grantedClaim_id', 'unique_payment_id');
    }
}
