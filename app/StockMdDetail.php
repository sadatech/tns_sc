<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockMdDetail extends Model
{
    protected $fillable = [
        'id_stock', 'id_product', 'oos'
    ];

    public function stock()
    {
        return $this->belongsTo('App\StockMdHeader', 'id_stock');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }
}
