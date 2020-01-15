<?php

namespace App;

use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductFocusArea extends Model
{
    use SoftDeletes, ValidationHelper;

    protected $fillable = [
        'id_product_focus', 'id_area', 'deleted_at'
    ];

    public static function rule()
    {
        return [
            'id_product_focus'  => 'required|integer',
            'id_area'           => 'required|integer',
        ];
    }

    public function productFocus()
    {
        return $this->belongsTo('App\ProductFocus', 'id_product_focus');
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'id_area');
    }
}
