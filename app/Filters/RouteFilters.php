<?php

namespace App\Filters;

use Illuminate\Http\Request;

class RouteFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    public function q($value = 'all') {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    public function type($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('type', $value) : null;
    }
}