<?php

namespace App;
use App\Components\traits\DropDownHelper;
use App\Filters\QueryFilters;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
    
    protected $fillable = [
		'id_route', 'id_employee', 'name', 'address', 'phone', 'active'
    ];
    
    public function route()
    {
        return $this->belongsTo('App\Route', 'id_route');
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