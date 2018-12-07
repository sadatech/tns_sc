<?php

namespace App;

use App\Components\traits\ValidationHelper;
use Illuminate\Database\Eloquent\Model;

class ProductFokusMtc extends Model
{
	use ValidationHelper;

	protected $fillable = [
		'id_product', 'id_area', 'id_channel', 'from', 'to'
	];

	public static function rule()
	{
		return [
			'id_product'    => 'required|integer',
			'from'          => 'required',
			'to'            => 'required'
		];
	}

	public function product()
	{
		return $this->belongsTo('App\Product', 'id_product');
	}

	public function area()
	{
		return $this->belongsTo('App\Area', 'id_area');
	}

	public function channel()
	{
		return $this->belongsTo('App\Channel', 'id_channel');
	}


	public static function hasActivePF($data, $self_id = null)
	{
		$products = ProductFokusMtc::where('id_product', $data['id_product'])
		->where('id_area', $data['id_area'])
		->where('id_channel', $data['id_channel'])
		->where('id', '!=', $self_id)
		->where(function($query) use ($data){
			$query->whereBetween('from', [$data['from'], $data['to']]);
			$query->orWhereBetween('to', [$data['from'], $data['to']]);
		})->count();

		return $products > 0;
	}

}
