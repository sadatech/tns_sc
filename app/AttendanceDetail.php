<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    protected $fillable = [
        'id_attendance', 'id_employee', 'id_store','id_place','checkin','checkout'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function attendance()
    {
    	return $this->belongsTo('App\Attendance', 'id_attendance');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }

    public function place()
    {
        return $this->belongsTo('App\Place', 'id_place');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['store_name1'] = $this->store->name1;
        $array['store_name2'] = $this->store->name2 ?? "-";
        $array['place_name'] = $this->place->name ?? "-";
        return $array;
    }
}
