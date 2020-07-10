<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FundsCenter extends Model
{
    protected $fillable = [
        'division', 'description','fundsCenterNumber','costCenter', 'fond','teachingUnit','faculty', 'professor','level',
    ];

    public function favored()
    {
        return $this->hasMany(GrantedClaim::class, 'favoredFundsCenter_id');
    }

    public function charged()
    {
        return $this->hasMany(GrantedClaim::class, 'chargedFundsCenter_id');
    }

    public function sqmPayment()
    {
        return $this->hasMany(SqmPayment::class);
    }
}
