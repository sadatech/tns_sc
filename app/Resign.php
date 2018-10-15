<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resign extends Model
{
	protected $fillable = [
		'id_store', 'resign_date', 'effective', 'alasan', 'penjelasan'
	];

	public function resignStore()
    {
        return $this->hasMany('App\ResignStore', 'id_resign');
    }

	public function store()
	{
		return $this->belongsTo('App\Store', 'id_store');
	}
}
