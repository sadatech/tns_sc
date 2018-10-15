<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailIn extends Model
{
    protected $fillable = [
        'id_sellin', 'id_product', 'qty', 'price', 'isPf'
    ];

    public function sellin()
    {
        return $this->belongsTo('App\SellIn', 'id_sellin');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

}
