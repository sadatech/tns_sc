<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Region extends Model
{
    protected $fillable = [
        'name'
    ];

    public function areas()
    {
    	return $this->hasMany('App\Area', 'id_region');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
