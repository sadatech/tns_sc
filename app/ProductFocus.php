<?php

namespace App;

use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class ProductFocus extends Model
{
    use SoftDeletes, ValidationHelper;

    protected $table = 'product_focus';

    protected $fillable = [
        'id_product', 'from', 'to', 'deleted_at'
    ];

    public static function rule()
    {
        return [
            'id_product'    => 'required|integer',
            'from'          => 'required',
            'to'            => 'required'
        ];
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function productFocusArea()
    {
        return $this->hasMany('App\ProductFocusArea', 'id_product_focus');
    }

    public function getFromAttribute($value)
    {
        return date('m/Y', strtotime($value));
    }

    public function getToAttribute($value)
    {
        return date('m/Y', strtotime($value));
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
