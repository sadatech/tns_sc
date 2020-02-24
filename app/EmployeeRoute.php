<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeRoute extends Model
{
    protected $fillable = [
        'id_employee', 'id_route'
    ];

    public function employee()
    {
		return $this->belongsTo('App\Employee', 'id_employee');
    }
    
    public function route()
    {
		return $this->belongsTo('App\Route', 'id_route');
    }
}
