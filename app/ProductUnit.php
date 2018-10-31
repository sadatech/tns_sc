<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
	protected $guarded = [];


    public function product()
    {
    	return $this->belongsTo('App\Product', 'product_id');
    }

    public function measure()
    {
    	return $this->belongsTo('App\SkuUnit', 'sku_unit_id');
    }
}
