<?php

namespace App\Components\traits;

use Illuminate\Http\Request;

/**
 * 
 */

trait ValidationHelper
{
	public static function validate($data)
	{
		return Validator($data, static::rule());
	}
}