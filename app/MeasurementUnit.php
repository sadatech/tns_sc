<?php

namespace App;

use App\Components\traits\DropDownHelper;
use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
	use DropDownHelper;
	use ValidationHelper;
	
    protected $guarded = [];

	public static function rule()
	{
		return [
			'name' => 'string|required',
			'size' => 'numeric|required'
		];
	}

    public function productMeasure()
    {
    	return $this->hasMany('App\ProductMeasure', 'id_measure');
    }

}