<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductFokus extends Model
{
    protected $fillable = [
        'id_product', 'id_area', 'type', 'from', 'to'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'id_area');
    }
}