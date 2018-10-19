<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'latitude', 'longitude', 'description', 'id_province', 'id_city', 'email', 'phone'
    ];

    public function attendanceDetail()
    {
        return $this->hasMany('App\AttendanceDetail', 'id_place');
    }
}
