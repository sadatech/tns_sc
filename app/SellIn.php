<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellIn extends Model
{
    protected $fillable = [
        'id_employee', 'id_store', 'date', 'week'
    ];

    public function detailIn()
    {
    	return $this->hasMany('App\DetailIn', 'id_sellin');
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
