<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rejoin extends Model
{
    protected $fillable = [
        'id_employee', 'join_date', 'alasan'
    ];
	
	public function employee()
	{
		return $this->belongsTo('App\Employee', 'id_employee');
	}

}
