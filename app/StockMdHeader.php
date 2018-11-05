<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockMdHeader extends Model
{
    protected $fillable = [
        'id_employee', 'id_pasar', 'date', 'week', 'stockist'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function pasar()
    {
        return $this->belongsTo('App\Pasar', 'id_pasar');
    }

    public function stockDetail()
    {
    	return $this->hasMany('App\StockMdDetail', 'id_stock');
    }
}
