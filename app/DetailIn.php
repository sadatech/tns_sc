<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailIn extends Model
{
    protected $fillable = [
        'id_sellin', 'id_product', 'id_measure', 'qty'
    ];

    public function sellin()
    {
        return $this->belongsTo('App\SellIn', 'id_sellin');
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
