<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailAvailability extends Model
{
    protected $table = 'detail_availability';

    public function availability(){
    	return $this->belongsTo(Availability::class);
    }
}
