<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesMdDetail extends Model
{
    protected $fillable = [
        'id_sales', 'id_product', 'qty', 'qty_actual', 'satuan'
    ];

    public function sales()
    {
        return $this->belongsTo('App\SalesMd', 'id_sales');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

}
