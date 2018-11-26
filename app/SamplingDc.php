<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SamplingDc extends Model
{
    protected $fillable = [
        'id_employee', 'place', 'date', 'week', 'type', 'name', 'phone'
    ];

    public function detailSampling()
    {
    	return $this->hasMany('App\SamplingDcDetail', 'id_sampling');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

}
