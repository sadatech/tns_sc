<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filters\QueryFilters;

class Outlet extends Model
{
    use SoftDeletes;
    
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

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}