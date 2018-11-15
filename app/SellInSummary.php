<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class SellInSummary extends Model
{
    protected $table ='sell_in_summary';

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id');
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
