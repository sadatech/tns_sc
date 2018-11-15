<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Carbon\Carbon;

class JobTraceFilters extends QueryFilters
{
    public function title($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('title', 'like', '%'.$value.'%') : null;
    }

    public function status($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('status', $value) : null;
    }

    public function date($value) {
    	if(!$this->requestAllData($value)){
    		return $this->builder->whereDate('date', Carbon::parse($value)->format('Y-m-d'));
    	}else{
    		return null;
    	}
    }
}