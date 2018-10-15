<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Area extends Model
{
    protected $fillable = [
        'name', 'id_region'
    ];

    public function region()
    {
        return $this->belongsTo('App\Region', 'id_region');
    }
    
    public function subareas()
    {
    	return $this->hasMany('App\SubArea', 'id_area');
    }  

    public function productfocus()
    {
        return $this->hasMany('App\ProductFokus', 'id_area');
    }

    public function productpromo()
    {
        return $this->hasMany('App\ProductPromo', 'id_area');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
