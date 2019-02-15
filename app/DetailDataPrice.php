<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailDataPrice extends Model
{
    protected $table = 'detail_data_price';
    
    protected $fillable = [
        'id_data_price', 'id_product', 'price'
    ];

    public function data_price(){
    	return $this->belongsTo('App\DataPrice', 'id_data_price');
    }

    public function product(){
    	return $this->belongsTo('App\Product', 'id_product');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['product_name'] = $this->product->name??'-';
        return $array;
    }
}
