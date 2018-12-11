<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
		'id_subarea', 'name'
    ];

    public function subarea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }
 	
 	public function block()
    {
    	return $this->hasMany('App\Block', 'id_route');
    }   
}
