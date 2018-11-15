<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetGtc extends Model
{
    protected $fillable = [
        'id_employee', 'rilis', 'value_sales', 'hk', 'ec', 'cbd'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }
}
