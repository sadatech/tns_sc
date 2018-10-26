<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanEmployee extends Model
{
    protected $fillable = [
        'id_employee', 'id_plandc'
    ];
    
    public function employee()
    {
		return $this->belongsTo('App\Employee', 'id_employee');
    }
    
    public function planDc()
    {
		return $this->belongsTo('App\PlanDc', 'id_plandc');
    }
}
