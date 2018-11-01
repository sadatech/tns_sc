<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesTiers extends Model
{
	protected $table ='sales_tiers';
	protected $fillable = ['id', 'name'];

	   public function stores()
    {
    	return $this->belongsTo('App\Store', 'id_salestier');
    }

}
