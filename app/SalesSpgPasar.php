<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesSpgPasar extends Model
{
    protected $fillable = [
        'id_employee', 'id_pasar', 'date', 'week', 'type', 'name', 'phone'
    ];

    public function detailSales()
    {
    	return $this->hasMany('App\SalesSpgPasarDetail', 'id_sales');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function pasar()
    {
        return $this->belongsTo('App\Pasar', 'id_pasar');
    }
}
