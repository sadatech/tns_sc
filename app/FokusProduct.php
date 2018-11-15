<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FokusProduct extends Model
{
    protected $fillable = [
        'id_product', 'id_pf'
    ];

    public function product()
    {
    	return $this->belongsTo('App\Product', 'id_product');
    }

    public function pf()
    {
    	return $this->belongsTo('App\ProductFokus', 'id_pf');
    }

}
