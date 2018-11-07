<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailAvailability extends Model
{
    protected $table = 'detail_availability';

    protected $fillable = [
        'id_availability', 'id_product', 'available'
    ];
    
    public function availability(){
    	return $this->belongsTo(Availability::class);
    }
    public function product(){
    	return $this->belongsTo(Product::class);
    }
}
