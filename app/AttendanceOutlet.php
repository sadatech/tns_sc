<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceOutlet extends Model
{
    protected $fillable = [
        'id_attendance', 'id_employee', 'id_outlet','checkin','checkout'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function attendance()
    {
    	return $this->belongsTo('App\Attendance', 'id_attendance');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'id_outlet');
    }
}
