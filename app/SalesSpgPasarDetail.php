<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesSpgPasarDetail extends Model
{
    protected $fillable = [
        'id_sales', 'id_product', 'qty', 'qty_actual', 'satuan', 'is_pf'
    ];

    public function sales()
    {
        return $this->belongsTo('App\SalesSpgPasar', 'id_sales');
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
