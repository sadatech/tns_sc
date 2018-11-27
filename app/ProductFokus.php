<?php

namespace App;

use DB;
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

    public static function hasActivePF($data, $self_id = null)
    {
        $x = DB::table('fokus_channels')->join('channels', 'fokus_channels.id_channel', '=', 'channels.id')->get();
        // $xp = DB::table('products')->join('fokus_products', 'products.id', '=', 'fokus_products.id_product')->get();
        // $y = count(collect($x, $data['channel'])->get('id'));
        // $xp = ProductFokus::f();
      
        // $channel = Channel::whereRaw("TRIM(UPPER(name)) = '". strtoupper($data['channel'])."'")->count();
        $fokus = ProductFokus::where('id', '!=', $self_id)
                                ->where(function($query) use ($data){
                                    $query->whereBetween('from', [$data['from'], $data['to']]);
                                    $query->orWhereBetween('to', [$data['from'], $data['to']]);
                                })->count();
$q = $x->whereIn(['id_channel' => $data['channel']])->where('id_pf', $fokus)->count();
        return  $q > 0;
    }
}