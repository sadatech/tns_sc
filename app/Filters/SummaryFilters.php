<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Carbon\Carbon;

class SummaryFilters extends QueryFilters
{
    public function id_reg($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('region_id', $value) : null;
    }

    public function id_ar($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('area_id', $value) : null;
    }

    public function id_sar($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('subarea_id', $value) : null;
    }

    public function id_str($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('store_id', $value) : null;
    }

    public function id_emp($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('employee_id', $value) : null;
    }

    public function date_range($value) {
    	if(!$this->requestAllData($value)){
    		return $this->builder->whereMonth('date', '=', Carbon::parse($value)->format('m'))
    							 ->whereDate('date', '<=', Carbon::parse($value)->format('Y'));
    	}else{
    		return null;
    	}
    }
}