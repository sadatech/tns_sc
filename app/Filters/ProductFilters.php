<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ProductFilters extends QueryFilters
{
    public function q($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%')->orWhere('code', 'like', '%'.$value.'%') : null;
    }

    public function product($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%')->orWhere('code', 'like', '%'.$value.'%') : null;
    }

	public function filter_sub_category($value = '') {
		if (!empty($value)) {
	    	return $this->builder->whereHas('subCategory', function ($query) use ($value){
	    		return $query->whereIn('id', $value);
	    	});
	    }
    }
    public function filter_category($value = '') {
		if (!empty($value)) {
	    	return $this->builder->whereHas('category', function ($query) use ($value){
	    		return $query->whereIn('id', $value);
	    	});
	    }
    }
    public function filter_brand($value = '') {
		if (!empty($value)) {
	    	return $this->builder->whereHas('brand', function ($query) use ($value){
	    		return $query->whereIn('id', $value);
	    	});
	    }
    }
}