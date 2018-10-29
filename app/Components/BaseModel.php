<?php

namespace App\Components;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
	public function isNewRecord()
	{
		return empty($this->id) ? true : false;
	}
}

