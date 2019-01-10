<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'id_employee', 'id_store', 'date', 'image1', 'image2', 'image3'
    ];

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }

}