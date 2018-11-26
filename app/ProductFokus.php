<?php

namespace App;

use App\Components\traits\DropDownHelper;
use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class ProductFokus extends Model
{
    use DropDownHelper;
    use ValidationHelper;

    protected $fillable = [
        'from', 'to'
    ];

    public static function rule()
    {
        return [
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

    public function Fokus()
    {
    	return $this->hasMany('App\FokusChannel', 'id_pf');
    }

    public function fokusarea()
    {
    	return $this->hasMany('App\FokusArea', 'id_area');
    }

    public function fokusproduct()
    {
    	return $this->hasMany('App\FokusProduct', 'id_product');
    }

    public function getFromAttribute($value)
    {
        return date('m/Y', strtotime($value));
    }

    public function getToAttribute($value)
    {
        return date('m/Y', strtotime($value));
    }

    // public static function hasActivePF($data, $self_id = null)
    // {
    //     $fokus = ProductFokus::where('id', '!=', $self_id)
    //                             ->where(function($query) use ($data){
    //                                 $query->whereBetween('from', [$data['from'], $data['to']]);
    //                                 $query->orWhereBetween('to', [$data['from'], $data['to']]);
    //                             })->count();

    //     return $fokus > 0;
    // }
}