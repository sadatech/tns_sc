<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistributionMotoricDetail extends Model
{
    protected $fillable = [
        'id_distribution', 'id_product', 'qty', 'qty_actual', 'satuan'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function distribution()
    {
    	return $this->belongsTo('App\DistributionMotoric', 'id_distribution');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['product_name'] = $this->product->name;
        return $array;
    }
}
