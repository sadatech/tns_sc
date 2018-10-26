<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailOut extends Model
{
    protected $fillable = [
		'id_sellout', 'id_store', 'id_measure', 'qty'
	];

	public function sellout()
	{
		return $this->belongsTo('App\SellOut', 'id_sellout');
	}

	public function store()
	{
		return $this->belongsTo('App\Store', 'id_store');
	}

	public function measure()
    {
        return $this->belongsTo('App\MeasurementUnit', 'id_measure');
    }

}