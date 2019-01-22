<?php

namespace App\Filters;

use Illuminate\Http\Request;

class SalesTierFilters extends QueryFilters
{

    public function q($value = 'all') {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }
}