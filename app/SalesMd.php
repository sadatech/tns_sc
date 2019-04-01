<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesMd extends Model
{
    use SoftDeletes;
    protected $table = 'sales_mds';

    protected $fillable = [
        'id_employee', 'id_outlet', 'date', 'week', 'type'
    ];

    public function detailSales()
    {
    	return $this->hasMany('App\SalesMdDetail', 'id_sales');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'id_outlet');
    }
}
