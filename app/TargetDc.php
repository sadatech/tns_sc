<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetDc extends Model
{
    protected $fillable = [
        'id_employee', 'id_subarea', 'rilis', 'value', 'value_pf'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function subArea()
    {
        return $this->belongsTo('App\SubArea', 'id_subarea');
    }
}
