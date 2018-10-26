<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pasar extends Model
{
    protected $fillable = [
        'name', 'phone', 'address', 'latitude', 'longitude', 'id_subarea'
    ];

    public function subarea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

    public function EmployeePasar()
    {
        return $this->hasMany('App\EmployeePasar', 'id_pasar');
    }

}