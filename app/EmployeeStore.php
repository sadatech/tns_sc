<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeStore extends Model
{
    protected $fillable = [
        'id_employee', 'id_store', 'alokasi'
    ];

    public function employee()
    {
		return $this->belongsTo('App\Employee', 'id_employee');
    }
    
    public function store()
    {
		return $this->belongsTo('App\Store', 'id_store');
    }

}