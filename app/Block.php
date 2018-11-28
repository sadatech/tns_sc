<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
		'id_subarea', 'name', 'address', 'phone', 'active'
    ];
    
    public function subArea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }
    
    public function attendanceBlock()
    {
        return $this->hasMany('App\AttendanceBlock', 'id_block');
    }
}