<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name', 'id_channel'
    ];

    public function channel()
    {
        return $this->belongsTo('App\Channel', 'id_channel');
    }

    public function stores()
    {
    	return $this->hasMany('App\Store', 'id_account');
    }
}

