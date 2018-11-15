<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSubArea extends Model
{
    protected $fillable = [
        'id_employee', 'id_subarea', 'isTl'
    ];

    public function employee()
    {
		return $this->belongsTo('App\Employee', 'id_employee');
    }
    
    public function subarea()
    {
		return $this->belongsTo('App\SubArea', 'id_subarea');
    }
}
