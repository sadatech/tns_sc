<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesMotoric extends Model
{
    protected $fillable = [
        'id_employee', 'id_block', 'date', 'week', 'type'
    ];

    public function detailSales()
    {
    	return $this->hasMany('App\SalesMotoricDetail', 'id_sales');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function block()
    {
        return $this->belongsTo('App\Block', 'id_block');
    }

}
