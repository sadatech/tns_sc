<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Position extends Model
{
    protected $fillable = [
        'name'
    ];
    
    public function employees()
    {
    	return $this->hasMany('App\Employee', 'id_position');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
