<?php

namespace App;

use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class ProductFokusMd extends Model
{

    use ValidationHelper;

    protected $fillable = [
        'id_product', 'from', 'to'
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


    public function getFromAttribute($value)
    {
        return date('m/Y', strtotime($value));
    }

    public function getToAttribute($value)
    {
        return date('m/Y', strtotime($value));
    }

    public static function hasActivePF($data, $self_id = null)
    {
        $products = ProductFokus::where('id_product', $data['id_product'])
                                ->where('id', '!=', $self_id)
                                ->where(function($query) use ($data){
                                    $query->whereBetween('from', [$data['from'], $data['to']]);
                                    $query->orWhereBetween('to', [$data['from'], $data['to']]);
                                })->count();

        return $products > 0;
    }
}
