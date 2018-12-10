<?php

namespace App;

use App\Components\traits\ValidationHelper;
use App\Components\traits\DropDownHelper;
use App\Filters\QueryFilters;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Product extends Model
{
    use SoftDeletes;
    use ValidationHelper;
    use DropDownHelper;
    
    protected $guarded = ['sku_units'];

    protected $dates =['deleted_at'];

    public static function boot(){
        self::creating(function($model){
            $model->id_brand = Brand::defaultBrand()->id;
        });
    }

    public static function rule()
    {
        return [
            'id_subcategory' => 'required|integer',
            'name' => 'required|string',
            'code' => 'required|string',
            'panel' => 'required|string',
            'stock_type_id' => 'required|integer',
            'carton' => 'integer',
            'pack' => 'integer',
            'pcs' => 'integer',
        ];
    }

    public static function getPanelOptions()
    {
        return [
            'yes' => 'Yes',
            'no' => 'No'
        ];
    }

    public function fokusproduct()
    {
    	return $this->hasMany('App\FokusProduct', 'id_product');
    }

    public function fokusGTC()
    {
    	return $this->hasMany('App\ProductFokusGtc', 'id_product');
    }

    public function subcategory()
    {
        return $this->belongsTo('App\SubCategory', 'id_subcategory');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand', 'id_brand');
    }
    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function stockType()
    {
        return $this->belongsTo(ProductStockType::class);
    }

    public function measure()
    {
        return $this->hasMany('App\ProductMeasure', 'id_product');
    }

    public function sku_units()
    {
        return $this->hasMany(ProductUnit::class);
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
