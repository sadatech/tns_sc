<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailDataPrice extends Model
{
    protected $table = 'detail_data_price';

    public function data_price(){
    	return $this->belongsTo(DataPrice::class);
    }
}
