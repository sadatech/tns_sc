<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'latitude', 'longitude', 'description', 'id_province', 'id_city', 'email', 'phone'
    ];

    public function province()
    {
        return $this->belongsTo('App\Province', 'id_province');
    }

    public function city()
    { 
        return $this->belongsTo('App\City', 'id_city');
    }

    public function attendanceDetail()
    {
        return $this->hasMany('App\AttendanceDetail', 'id_place');
    }
}
