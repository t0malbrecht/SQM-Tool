<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'date', 'number','yearNumberCombined',
    ];

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

}
