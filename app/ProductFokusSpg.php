<?php

namespace App;

use App\Components\traits\DropDownHelper;
use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class ProductFokusSpg extends Model
{
    use DropDownHelper;
    use ValidationHelper;

    protected $fillable = [
        'id_product', 'id_employee', 'from', 'to'
    ];

    public static function rule()
    {
        return [
            'id_employee'   => 'required',
            'id_product'    => 'required',
            'from'          => 'required',
            'to'            => 'required'
        ];
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'id_product');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    // public function Fokus()
    // {
    // 	return $this->hasMany('App\FokusChannel', 'id_pf');
    // }

    // public function fokusarea()
    // {
    // 	return $this->hasMany('App\FokusArea', 'id_area');
    // }

    // public function fokusproduct()
    // {
    // 	return $this->hasMany('App\FokusProduct', 'id_product');
    // }

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
        foreach ($data['id_product'] as $key => $id_product){
            $products = ProductFokusSpg::where('deleted_at',null)
                                    ->where('id_product', $id_product)
                                    ->where('id_employee', $data['id_employee'])
                                    ->where('id', '!=', $self_id)
                                    // ->where(function($query) use ($data,$key){
                                    //     $query->whereBetween('from', [$data['from'][$key], $data['to'][$key]]);
                                    //     $query->orWhereBetween('to', [$data['from'][$key], $data['to'][$key]]);
                                    // })
                                    ->count();

            return $products > 0;
        }
    }
}