<?php

namespace App;

use App\Components\traits\DropDownHelper;
use Illuminate\Database\Eloquent\Model;

class ProductStockType extends Model
{
	use DropDownHelper;

	protected $fillable = ['name', 'value'];
}
