<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubArea extends Model
{
    protected $fillable = [
		'name', 'id_area'
    ];
    
    public function area()
    {
        return $this->belongsTo('App\Area', 'id_area');
    }

    public function stores()
    {
        return $this->hasMany('App\Store', 'id_subarea');
    }

    public function employeeSubArea()
    {
        return $this->hasMany('App\employeeSubArea', 'id_subarea');
    }

    public function employee()
    {
        return $this->hasMany('App\Employee', 'id_subarea');
    }
}
