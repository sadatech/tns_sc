<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'id_province', 'name'
    ];

    public function places()
    {
    	return $this->hasMany('App\Place', 'id_city');
    }

    public function stores()
    {
    	return $this->hasMany('App\Store', 'id_city');
    }   
}
