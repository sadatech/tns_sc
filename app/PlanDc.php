<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanDc extends Model
{
    protected $fillable = [
        'id_employee', 'date', 'lokasi', 'stocklist', 'channel'
    ];

    public function planEmployee()
    {
        return $this->belongsTo('App\PlanEmployee', 'id_plandc');
    }
}
