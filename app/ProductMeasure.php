<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductMeasure extends Model
{
    protected $fillable = [
        'id_product', 'id_measure'
    ];

    public function product()
    {
    	return $this->belongsTo('App\Product', 'id_product');
    }

    public function measure()
    {
    	return $this->belongsTo('App\MeasurementUnit', 'id_measure');
    }

}
