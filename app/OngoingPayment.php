<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OngoingPayment extends GrantedClaim
{
    protected $fillable = [
        'claim_id', 'christmasBonus', 'favoredFundsCenter_id', 'chargedFundsCenter_id', 'costType_id', 'grantedFunds', 'plannedStartDate', 'plannedEndDate', 'notes', 'due', 'requirements'
    ];

    public function ongoingPaymentHistories()
    {
        return $this->hasMany(OngoingPaymentHistory::class);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->unique_payment_id = 'OGP-' . $model->id;
            $model->save();
        });
    }
}
