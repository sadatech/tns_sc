<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OosDetail extends Model
{
    protected $fillable = [
        'id_oos', 'id_product', 'qty',
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function oos()
    {
    	return $this->belongsTo('App\Oos', 'id_oos');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['product_name'] = $this->product->name;
        return $array;
    }
}
