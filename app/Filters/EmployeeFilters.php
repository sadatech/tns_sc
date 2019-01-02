<?php

namespace App\Filters;

use Illuminate\Http\Request;

class EmployeeFilters extends QueryFilters
{

    /**
     * Ordering data by employee nik & name
     */
    public function employee($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('name', 'like', '%'.$value.'%')->orWhere('nik', 'like', '%'.$value.'%') : null;
    }

    public function roleGroup($value){
    	return $this->builder->whereHas('position', function ($query) use ($value){
    		return $query->whereIn('level', $value);
    	});
    }

    public function employeeMtc() {
        return $this->builder->whereHas('position', function($query){
                return $query->whereIn('level', ['spgmtc', 'mdmtc']);
            });
    }
}