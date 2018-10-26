<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    protected $fillable = [
        'name', 'size'
    ];

    public function productMeasure()
    {
    	return $this->hasMany('App\ProductMeasure', 'id_measure');
    }

}
