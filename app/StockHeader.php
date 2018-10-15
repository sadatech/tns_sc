<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockHeader extends Model
{
    protected $fillable = [
        'id_employee', 'id_store', 'date', 'week'
    ];
}
