<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResignStore extends Model
{
    protected $fillable = [
		'id_store', 'id_resign'
	];

	public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }
    public function resign()
    {
        return $this->belongsTo('App\Resign', 'id_resign');
    }
}
