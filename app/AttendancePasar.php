<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendancePasar extends Model
{
	protected $fillable = [
		'id_attendance', 'id_employee', 'id_pasar','checkin','checkout'
	];

	public function employee()
	{
		return $this->belongsTo('App\Employee', 'id_employee');
	}

	public function attendance()
	{
		return $this->belongsTo('App\Attendance', 'id_attendance');
	}

	public function pasar()
	{
		return $this->belongsTo('App\Pasar', 'id_pasar');
	}

	public function toArray(){
        $array = parent::toArray();
        $array['pasar_name'] = $this->pasar->name ?? null;
        return $array;
    }
}
