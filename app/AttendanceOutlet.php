<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class AttendanceOutlet extends Model
{
    protected $fillable = [
        'id_attendance', 'id_employee', 'id_outlet','checkin','checkout'
    ];
    
    // protected $appends = ['outlet_name'];

    // public function getOutletNameAttribute()
    // {
    //     return $this->outlet->name;
    // }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function attendance()
    {
    	return $this->belongsTo('App\Attendance', 'id_attendance');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'id_outlet');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['outlet_name'] = $this->outlet->name ?? null;
        return $array;
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
