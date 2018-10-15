<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailOut extends Model
{
    protected $fillable = [
		'id_sellout', 'id_store', 'date', 'week'
	];

	public function sellout()
	{
		return $this->belongsTo('App\SellOut', 'id_sellout');
	}

	public function store()
	{
		return $this->belongsTo('App\Store', 'id_store');
	}

}
