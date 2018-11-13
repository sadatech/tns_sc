<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class SalesMtcSummary extends Model
{
    protected $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
