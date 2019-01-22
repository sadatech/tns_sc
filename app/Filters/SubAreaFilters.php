<?php

namespace App\Filters;

use Illuminate\Http\Request;

class SubAreaFilters extends QueryFilters
{

    /**
     * Ordering data by name
     */
    public function name($value = 'all') {
        return (!$this->requestAllData($value)) ? $this->builder->where('sub_areas.name', 'like', '%'.$value.'%') : null;
    }

    public function q($value = 'all') {
        return (!$this->requestAllData($value)) ? $this->builder->where('sub_areas.name', 'like', '%'.$value.'%') : null;
    }
}