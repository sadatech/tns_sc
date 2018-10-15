<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name', 'keterangan'
    ];

    public function product()
    {
    	return $this->hasMany('App\Product', 'id_brand');
    }

}
