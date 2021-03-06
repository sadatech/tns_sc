<?php

namespace App;

use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductFokusGtc extends Model
{
    use SoftDeletes;

    use ValidationHelper;
    protected $fillable = [
        'id_product', 'id_area', 'from', 'to'
    ];

    protected $dates = ['deleted_at'];

    public static function rule()
    {
        return [
            'id_product'       => 'required|integer',
            'from'          => 'required',
            'to'            => 'required'
        ];
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function area()
    {
        return $this->belongsTo('App\Area', 'id_area');
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
        $products = ProductFokusGtc::where('id_product', $data['id_product'])
                                ->where('id_area', (isset($data['area']) ? $data['id_area'] : null)) 
                                ->where('id', '!=', $self_id)
                                ->where(function($query) use ($data){
                                    $query->whereBetween('from', [$data['from'], $data['to']]);
                                    $query->orWhereBetween('to', [$data['from'], $data['to']]);
                                })->count();

        return $products > 0;
    }
}
