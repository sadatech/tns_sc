<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'id_employee', 'id_store', 'date', 'week'
    ];
}
