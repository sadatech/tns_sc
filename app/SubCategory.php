<?php

namespace App;

use App\Components\traits\DropDownHelper;
use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class SubCategory extends Model
{
    use DropDownHelper;

    protected $fillable = [
        'name', 'description', 'id_category'
    ];

    public function category()
    {
        return $this->belongsTo('App\Category', 'id_category');
    }
    
    public function products()
    {
    	return $this->hasMany('App\Product', 'id_subcategory');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
