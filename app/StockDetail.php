<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockDetail extends Model
{
    protected $fillable = [
        'id_stock', 'id_product', 'qty', 'price'
    ];

    public function stock()
    {
        return $this->belongsTo('App\Stock', 'id_stock');
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
