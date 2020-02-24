<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductFocusFilters extends QueryFilters
{
	public function filter_product($value = '') {
		if (!empty($value)) {
			return (!$this->requestAllData($value)) ? $this->builder->whereIn('id_product', $value) : null;
		}
	}
	public function filter_area($value = '') {
		if (!empty($value)) {
	    	return $this->builder->whereHas('productFocusArea', function ($query) use ($value){
	    		return $query->whereIn('id_area', $value);
	    	});
	    }
    }
    public function filter_check_all_area($value = '') {
		if ($value == 'true') {
	    	return $this->builder->whereDoesntHave('productFocusArea');
	    }
    }
    public function filter_start_month($value = '') {
		if (!empty($value)) {
	        $date   = Carbon::createFromFormat('d/m/Y','01/'.$value)->format('Y-m-d');
	        return (!$this->requestAllData($value)) ? $this->builder->where('from', '>=', $date) : null;
	    }
    }
    public function filter_end_month($value = '') {
		if (!empty($value)) {
	        $date   = Carbon::createFromFormat('d/m/Y','01/'.$value)->endOfMonth()->format('Y-m-d');
	        return (!$this->requestAllData($value)) ? $this->builder->where('to', '<=', $date) : null;
	    }
    }
}