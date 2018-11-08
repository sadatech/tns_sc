<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailSales extends Model
{
    protected $fillable = [
        'id_sales', 'id_product', 'id_measure', 'qty', 'qty_actual', 'satuan'
    ];

    public function sellin()
    {
        return $this->belongsTo('App\Sales', 'id_sales');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function measure()
    {
        return $this->belongsTo('App\MeasurementUnit', 'id_measure');
    }

}
