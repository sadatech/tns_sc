<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'id_product', 'type_toko', 'type_price', 'rilis', 'price'
    ];

    public function product()
	{
		return $this->belongsTo('App\Product', 'id_product');
	}
}
