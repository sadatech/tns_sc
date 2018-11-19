<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class SalesMtcSummary extends Model
{
    protected $table = 'sales_mtc_summary';

    public function employee()
    {
        return $this->belongsTo('App\Employee', 'id_employee');
    }

    public function product()
    {
    	return $this->belongsTo('App\Product', 'id_product');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'id_store');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
