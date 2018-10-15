<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TargetDetail extends Model
{
    protected $fillable = [
        'id_target', 'id_brand', 'value', 'value_pf'
    ];

    public function target()
    {
        return $this->belongsTo('App\Target', 'id_target');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand', 'id_brand');
    }
}
