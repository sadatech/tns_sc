<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = [
		'id_employee_pasar', 'name', 'phone', 'active'
    ];
    
    public function employeePasar()
    {
        return $this->belongsTo('App\EmployeePasar', 'id_employee_pasar');
    }
}
