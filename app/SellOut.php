<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellOut extends Model
{
    protected $fillable = [
		'id_employee', 'id_store', 'date', 'week'
	];

	public function detailOut()
	{
        return $this->hasMany('App\DetailOut', 'id_sellout');
	}

	public function employee()
	{
		return $this->belongsTo('App\Employee', 'id_employee');
	}

	public function store()
	{
		return $this->belongsTo('App\Store', 'id_store');
	}

}
