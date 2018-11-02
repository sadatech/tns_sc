<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreDistributor extends Model
{
    protected $fillable = [
        'id_store', 'id_distributor'
    ];

    public function distributor()
    {
		return $this->belongsTo('App\Distributor', 'id_distributor');
    }
    
    public function store()
    {
		return $this->belongsTo('App\Store');
    }

}
