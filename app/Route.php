<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Route extends Model
{
    protected $fillable = [
		'id_subarea', 'name', 'address', 'latitude', 'longitude', 'type'
    ];

    public function subarea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }
 	
 	public function block()
    {
    	return $this->hasMany('App\Block', 'id_route');
    }   

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
