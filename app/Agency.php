<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Agency extends Model
{
    protected $fillable = [
        'name'
    ];

    public function employees()
    {
    	return $this->hasMany('App\Employee', 'id_agency');
    }
        
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

}
