<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'date',
    ];

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

}
