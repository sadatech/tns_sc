<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $fillable = [
        'name'
    ];

    public function stores()
    {
    	return $this->hasMany('App\Store', 'id_classification');
    }   
}
