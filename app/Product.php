<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Product extends Model
{
    protected $fillable = [
        'name', 'deskripsi', 'id_subcategory', 'id_brand', 'panel'
    ];

    public function subCategory()
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
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
