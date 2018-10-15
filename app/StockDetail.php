<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockDetail extends Model
{
    protected $fillable = [
        'id_stock', 'id_product', 'qty', 'price'
    ];

}
