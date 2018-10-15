<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function subCategory()
    {
    	return $this->hasMany('App\SubCategory', 'id_category');
    }

}
