<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Components\traits\DropDownHelper;

class Category extends Model
{
    use DropDownHelper;
    protected $fillable = [
        'name', 'description'
    ];

    public function subCategory()
    {
    	return $this->hasMany('App\SubCategory', 'id_category');
    }

    public function Pf()
    {
    	return $this->hasMany('App\Pf', 'id_category1');
    }

    public function Pf2()
    {
    	return $this->hasMany('App\Pf', 'id_category2');
    }
}
