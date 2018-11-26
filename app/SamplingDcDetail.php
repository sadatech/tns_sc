<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SamplingDcDetail extends Model
{
    protected $fillable = [
        'id_sampling', 'id_product', 'qty', 'qty_actual', 'satuan', 'is_pf', 'is_target'
    ];

    public function sampling()
    {
        return $this->belongsTo('App\SamplingDc', 'id_sampling');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['product_name'] = $this->product->name;
        return $array;
    }

}
