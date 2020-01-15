<?php

namespace App;

use App\Components\BaseModel;
use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductPrice extends BaseModel
{
    use ValidationHelper, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id_product', 'release', 'retailer_price', 'consumer_price'
    ];

    public function product()
	{
		return $this->belongsTo('App\Product', 'id_product');
    }
    
    public static function rule()
    {
        return [
            'id_product'        => 'required',
            'release'           => 'required',
            'retailer_price'    => 'required',
            'consumer_price'    => 'required'
        ];
    }
}
