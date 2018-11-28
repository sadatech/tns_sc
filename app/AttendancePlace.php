<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendancePlace extends Model
{
    protected $fillable = [
        'id_attendance', 'id_employee', 'id_place','checkin','checkout'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function attendance()
    {
    	return $this->belongsTo('App\Attendance', 'id_attendance');
    }

    public function place()
    {
        return $this->belongsTo('App\Place', 'id_place');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['place_name'] = $this->place->name;
        $array['place_code'] = $this->place->code;
        return $array;
    }
}
