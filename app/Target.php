<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = ['id_employee', 'id_store', 'rilis', 'value', 'value_pf'];

    public function targetDetail()
    {
    	return $this->hasMany('App\TargetDetail', 'id_target');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }
}
