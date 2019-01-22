<?php

namespace App\Filters;

use Illuminate\Http\Request;

class PositionFilters extends QueryFilters
{
    public function q($value = 'all') {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    public function name($value = 'all') {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%') : null;
    }

    public function byId($value = []) {
        return (!$this->requestAllData($value)) ? $this->builder->whereNotIn('id', $value) : null;
    }
}