<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'id_employee','date','keterangan'
    ];

    public function employee()
    {
    	return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function attendanceDetail()
    {
        return $this->hasMany('App\AttendanceDetail', 'id_attendance');
    }
  
    public function attendanceOutlet()
    {
        return $this->hasMany('App\AttendanceOutlet', 'id_attendance');
    }

    public function attendancePasar()
    {
        return $this->hasMany('App\AttendancePasar', 'id_attendance');
    }

    public function attendancePlace()
    {
        return $this->hasMany('App\AttendancePlace', 'id_attendance');
    }

    public function attendanceBlock()
    {
        return $this->hasMany('App\AttendanceBlock', 'id_attendance');
    }
}
