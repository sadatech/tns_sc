<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'name', 'level'
    ];
    
    public function employees()
    {
    	return $this->hasMany('App\Employee', 'id_position');
    }
}
