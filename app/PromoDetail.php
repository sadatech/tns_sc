<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PromoDetail extends Model
{
    protected $fillable = [
        'id_promo', 'id_product', 'type', 'description', 'start_date', 'end_date'
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function productCompetitor()
    {
        return $this->belongsTo('App\ProductCompetitor', 'id_product_competitor');
    }

    public function promo()
    {
        return $this->belongsTo('App\Promo', 'id_promo');
    }
}
