<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;
use DB;

class Product extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'deskripsi', 'id_brand', 'id_category'
    ];

    protected $dates =['deleted_at'];

    public function subcategory()
    {
        return $this->belongsTo('App\SubCategory', 'id_subcategory');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand', 'id_brand');
    }
    
    public function prices()
    {
        return $this->hasMany('App\Price', 'id_product');
    }

    public function detailiin()
    {
        return $this->hasMany('App\DetailIn', 'id_product');
    }

    public function productFokus()
    {
        return $this->hasMany('App\ProductFokus', 'id_product');
    }

    public function productPromo()
    {
        return $this->hasMany('App\ProductPromo', 'id_product');
    }

    public function promoProduct()
    {
        return $this->hasMany('App\PromoProduct', 'id_product');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

    public function getPrice($date, $type_price){
        $data = $this->prices->where('rilis', '<=', $date)
                            ->where('type_price', $type_price)
                            ->sortByDesc('rilis')
                            ->first();

        return ($data) ? $data->price : 0;
    }
}
