<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FokusProduct extends Model
{
    protected $fillable = [
        'id_product', 'id_pf'
    ];

    protected $dates = ['deleted_at'];

    public function product()
    {
    	return $this->belongsTo('App\Product', 'id_product');
    }

    public function pf()
    {
    	return $this->belongsTo('App\ProductFokus', 'id_pf');
    }

}
