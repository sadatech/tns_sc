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
        return $this->hasMany('App\StockDetail', 'id_stock');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['pasar_name'] = $this->pasar->name;
        return $array;
    }
}
