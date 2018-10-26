<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSubArea extends Model
{
    protected $fillable = [
        'id_employee', 'id_subarea'
    ];

    public function employee()
    {
		return $this->belongsTo('App\Employee', 'id_employee');
    }
    
    public function store()
    {
		return $this->belongsTo('App\SubArea', 'id_subarea');
    }
}
