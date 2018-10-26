<?php

namespace App;

use App\Components\traits\DropDownHelper;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
	use DropDownHelper;
	
    protected $fillable = [
        'name', 'keterangan'
    ];

    public static function defaultBrand()
    {
    	return self::get()->first();
    }

    public function product()
    {
    	return $this->hasMany('App\Product', 'id_brand');
    }

}
