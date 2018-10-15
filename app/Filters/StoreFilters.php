<?php

namespace App\Filters;

use Illuminate\Http\Request;

class StoreFilters extends QueryFilters
{

    public function store($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name1', 'like', '%'.$value.'%')->orWhere('name2', 'like', '%'.$value.'%') : null;
    }
}