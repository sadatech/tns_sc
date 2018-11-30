<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesSpgSummaryFilters extends QueryFilters
{
    public function id_sub_cat($value) {
        return $this->builder;
        return (!$this->requestAllData($value)) ? 
            $this->builder->whereHas('store.subarea.area.region', function($query) use ($value){ 
                return $query->where('id', $value);
            })
            : null;
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