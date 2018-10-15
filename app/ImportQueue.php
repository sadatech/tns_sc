<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportQueue extends Model
{
    protected $fillable = [
        'name', 'id_employee', 'date', 'file', 'type', 'status', 'log'
    ];

    public function employee()
    {
    	return $this->hasMany('App\Employee', 'id_employee');
    }

}
