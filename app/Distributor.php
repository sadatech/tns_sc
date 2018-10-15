<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $fillable = [
        'name', 'code'
    ];

    public function stores()
    {
    	return $this->hasMany('App\StoreDistributor', 'id_distributor');
    }
}
