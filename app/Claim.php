<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'title', 'ioa', 'printNumber', 'claimant', 'document', 'meeting_id'
    ];

    public function oneTimePayment()
    {
        return $this->hasOne(OneTimePayment::class, 'claim_id');
    }

    public function ongoingPayment()
    {
        return $this->hasOne(OngoingPayment::class, 'claim_id');
    }

    public function meeting()
    {
        return $this->belongsTo(Meeting::class, 'meeting_id');
    }
}
