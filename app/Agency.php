<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = [
        'name'
    ];

    public function employees()
    {
    	return $this->hasMany('App\Employees', 'id_agency');
    }

}
