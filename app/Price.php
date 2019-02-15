<?php

namespace App;

use App\Components\BaseModel;
use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Price extends BaseModel
{
    use ValidationHelper, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'id_product', 'rilis', 'price'
    ];

    public function product()
	{
		return $this->belongsTo('App\Product', 'id_product');
    }
    
    public static function rule()
    {
        return [
            'rilis'                 => 'required',
            'id_product'            => 'required',
            'price'                 => 'required'
        ];
    }
    public static function hasActivePF($data, $self_id = null)
    {
        $price = Price::where('id', '!=', $self_id)
                                ->where('id_product', $data['id_product'])
                                ->where('rilis', $data['rilis'])->count();

        return $price > 0;
    }
}
