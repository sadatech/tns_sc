<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetGtc extends Model
{
    protected $fillable = [
        'id_employee', 'id_pasar', 'rilis', 'value', 'value_pf'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function pasar()
    {
        return $this->belongsTo('App\Pasar', 'id_pasar');
    }
}
