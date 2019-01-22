<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class SalesTiers extends Model
{
	protected $table ='sales_tiers';
	protected $fillable = ['id', 'name'];

	   public function stores()
    {
    	return $this->belongsTo('App\Store', 'id_salestier');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
