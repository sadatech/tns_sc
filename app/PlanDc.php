<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanDc extends Model
{
    protected $fillable = [
        'id_employee', 'date', 'lokasi', 'stocklist'
    ];

    public function planEmployee()
    {
        return $this->hasMany('App\PlanEmployee', 'id_plandc');
    }
}
