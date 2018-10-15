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
}
