<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesDc extends Model
{
    protected $fillable = [
        'id_employee', 'place', 'date', 'week', 'type', 'name', 'phone'
    ];

    public function detailSales()
    {
    	return $this->hasMany('App\SalesDcDetail', 'id_sales');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

}
