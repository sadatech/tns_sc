<?php

namespace App;

use App\Components\BaseModel;

class Price extends BaseModel
{
    protected $fillable = [
        'id_product', 'rilis', 'price'
    ];

    public function product()
	{
		return $this->belongsTo('App\Product', 'id_product');
	}
}
