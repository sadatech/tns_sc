<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'name'
    ];

    public function places()
    {
    	return $this->hasMany('App\Place', 'id_province');
    }

    public function stores()
    {
    	return $this->hasMany('App\Store', 'id_province');
    }
}
    