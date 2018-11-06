<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'id_employee', 'id_store', 'date', 'week', 'type'
    ];

    public function detailSales()
    {
    	return $this->hasMany('App\DetailSales', 'id_sales');
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
