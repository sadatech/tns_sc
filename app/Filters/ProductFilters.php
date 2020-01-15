<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ProductFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function product($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%')->orWhere('code', 'like', '%'.$value.'%') : null;
    }
}