<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceBlock extends Model
{
    protected $fillable = [
        'id_attendance', 'id_employee', 'id_block','checkin','checkout'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function attendance()
    {
    	return $this->belongsTo('App\Attendance', 'id_attendance');
    }

    public function block()
    {
        return $this->belongsTo('App\Block', 'id_block');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['block_name'] = $this->block->name;
        return $array;
    }
}
