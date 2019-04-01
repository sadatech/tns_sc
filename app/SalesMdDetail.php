<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesMdDetail extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id_sales', 'id_product', 'qty', 'qty_actual', 'satuan', 'is_pf', 'is_target'
    ];

    public function sales()
    {
        return $this->belongsTo('App\SalesMd', 'id_sales');
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
