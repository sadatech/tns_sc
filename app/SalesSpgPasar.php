<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SalesSpgPasar;
use Carbon\Carbon;
use App\Product;
use App\Price;
use DB;
use App\ProductFokusSpg;

class SalesSpgPasar extends Model
{
    protected $table = 'sales_spg_pasars';

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
