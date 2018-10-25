<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    protected $fillable = [
        'name', 'timezone'
    ];

    public function employees()
    {
    	return $this->hasMany('App\Employees', 'id_timezone');
    }
}
