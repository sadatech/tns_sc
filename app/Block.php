<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
		'id_subarea', 'id_employee', 'name', 'address', 'phone', 'active'
    ];
    
    public function subArea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }
    
    public function attendanceBlock()
    {
        return $this->hasMany('App\AttendanceBlock', 'id_block');
    }
}