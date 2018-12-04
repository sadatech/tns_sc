<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesRecap extends Model
{
    protected $fillable = [
        'id_outlet','id_employee','date','total_buyer','total_sales','total_value','photo'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'id_outlet');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['outlet_name'] = $this->outlet->name;
        return $array;
    }
}
