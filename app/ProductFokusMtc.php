<?php

namespace App;

use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class ProductFokusMtc extends Model
{
    use ValidationHelper;

    protected $fillable = [
        'id_product', 'id_area', 'id_channel', 'from', 'to'
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
    
    public function area()
    {
        return $this->belongsTo('App\Area', 'id_area');
    }
    
    public function channel()
    {
        return $this->belongsTo('App\Channel', 'id_channel');
    }

}
