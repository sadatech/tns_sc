<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MtcReportTemplate extends Model
{
    protected $fillable = [
        'id_employee','id_store','date','id_product'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function product()
    {
    	return $this->belongsTo('App\Product', 'id_product');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }
}
