<?php

namespace App;

use App\Components\traits\DropDownHelper;
use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class SkuUnit extends Model
{
	use DropDownHelper;
	use ValidationHelper;

	protected $guarded = [];

	public static function rule()
	{
		return [
			'name' => 'string|required',
			'conversion_value' => 'numeric|required'
		];
	}

}
