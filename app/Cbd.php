<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cbd extends Model
{
    protected $fillable = [
        'id_employee', 'id_outlet', 'date', 'photo'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function outlet()
    {
        return $this->belongsTo('App\Outlet', 'id_outlet');
    }
}
