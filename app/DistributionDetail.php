<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DistributionDetail extends Model
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
    	return $this->belongsTo('App\Distribution', 'id_distribution');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['product_name'] = $this->product->name;
        return $array;
    }
}
