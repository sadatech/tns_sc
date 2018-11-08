<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FokusArea extends Model
{
    protected $fillable = [
        'id_area', 'id_pf'
    ];

    public function area()
    {
    	return $this->belongsTo('App\Area', 'id_area');
    }

    public function pf()
    {
    	return $this->belongsTo('App\ProductFokus', 'id_productfokus');
    }

}
