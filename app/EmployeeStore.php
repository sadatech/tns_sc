<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class EmployeeStore extends Model
{
    protected $fillable = [
        'id_employee', 'id_store'
    ];

    public function employee()
    {
		return $this->belongsTo('App\Employee', 'id_employee');
    }
    
    public function store()
    {
		return $this->belongsTo('App\Store', 'id_store');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }

}