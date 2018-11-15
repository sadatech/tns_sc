<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Carbon\Carbon;

class SummaryFilters extends QueryFilters
{
    public function id_reg($value) {
        return (!$this->requestAllData($value)) ? 
            $this->builder->whereHas('store.subarea.area.region', function($query) use ($value){ 
                return $query->where('id', $value);
            })
            : null;
    }

    public function id_ar($value) {
        return (!$this->requestAllData($value)) ? 
            $this->builder->whereHas('store.subarea.area', function($query) use ($value){ 
                return $query->where('id', $value);
            })
            : null;
    }

    public function id_sar($value) {
        return (!$this->requestAllData($value)) ? 
            $this->builder->whereHas('store.subarea', function($query) use ($value){ 
                return $query->where('id', $value);
            })
            : null;
    }

    public function id_str($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('id_store', $value) : null;
    }

    public function id_emp($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('id_employee', $value) : null;
    }

    public function periode($value) {
    	if(!$this->requestAllData($value)){
    		return $this->builder->whereMonth('date', '=', Carbon::parse($value)->format('m'))
    							 ->whereYear('date', '=', Carbon::parse($value)->format('Y'));
    	}else{
    		return null;
    	}
    }
}