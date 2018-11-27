<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanDc extends Model
{
    protected $fillable = [
        'date', 'stocklist', 'channel', 'plan'
    ];

    public function planEmployee()
    {
        return $this->belongsTo('App\PlanEmployee', 'id_plandc');
    }
}
