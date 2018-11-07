<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $fillable = [
		'id_employee_pasar', 'name', 'phone', 'address', 'new_ro', 'customer_code', 'active'
    ];
    
    public function employeePasar()
    {
        return $this->belongsTo('App\EmployeePasar', 'id_employee_pasar');
    }
    
    public function attendanceOutlet()
    {
        return $this->hasMany('App\AttendanceOutlet', 'id_outlet');
    }
}