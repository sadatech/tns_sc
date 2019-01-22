<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class Timezone extends Model
{
    protected $fillable = [
        'name', 'timezone'
    ];

    public function employees()
    {
    	return $this->hasMany('App\Employee', 'id_timezone');
    }

	   public function stores()
    {
    	return $this->belongsTo('App\Store', 'id_timezone');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
