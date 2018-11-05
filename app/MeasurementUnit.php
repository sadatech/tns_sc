<?php

namespace App;

use App\Components\traits\DropDownHelper;
use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
	use DropDownHelper;
	
    protected $fillable = [
        'name', 'size'
    ];

    public function productMeasure()
    {
    	return $this->hasMany('App\ProductMeasure', 'id_measure');
    }

}
