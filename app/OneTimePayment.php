<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OneTimePayment extends GrantedClaim
{
    protected $fillable = [
        'id', 'claim_id', 'favoredFundsCenter_id', 'chargedFundsCenter_id', 'grantedFunds', 'spentFunds', 'spentDate', 'notes', 'costType_id', 'requirements', 'dueDate',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->unique_payment_id = 'OTP-' . $model->id;
            $model->save();
        });
    }

    }
