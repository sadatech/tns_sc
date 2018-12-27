<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceFilters extends QueryFilters
{
	public function periode($value) {
		return (!$this->requestAllData($value)) ? $this->builder->whereMonth('checkin', substr($value, 0, 2))->whereYear('checkin', substr($value, 3))
		: null;
	}

	public function employee($value)
	{
		return (!$this->requestAllData($value)) ?
		$this->builder->whereHas('attendance.employee', function($q) use ($request){
			return $q->where('id_employee', $value);
		})
		: null;
	}

	

	public function id_reg($value) {
        return (!$this->requestAllData($value)) ? 
            $this->builder->where(function ($queryFirst) use ($value){
            	return $queryFirst->whereHas('employeeStore.store.subarea.area.region', function($query) use ($value){ 
	                return $query->where('id', $value);
	            })->orWhereHas('attendance.attendanceDetail.store.subarea.area.region', function($query) use ($value){ 
	                return $query->where('id', $value);
	            });
            })            
            : null;
    }

    public function id_ar($value) {
        return (!$this->requestAllData($value)) ? 
            $this->builder->where(function ($queryFirst) use ($value){
            	return $queryFirst->whereHas('employeeStore.store.subarea.area', function($query) use ($value){ 
	                return $query->where('id', $value);
	            })->orWhereHas('attendance.attendanceDetail.store.subarea.area', function($query) use ($value){ 
	                return $query->where('id', $value);
	            });
            })            
            : null;
    }

    public function id_sar($value) {
        return (!$this->requestAllData($value)) ? 
            $this->builder->where(function ($queryFirst) use ($value){
            	return $queryFirst->whereHas('employeeStore.store.subarea', function($query) use ($value){ 
	                return $query->where('id', $value);
	            })->orWhereHas('attendance.attendanceDetail.store.subarea', function($query) use ($value){ 
	                return $query->where('id', $value);
	            });
            })            
            : null;
    }

    public function id_str($value) {
        return (!$this->requestAllData($value)) ? 
            $this->builder->where(function ($queryFirst) use ($value){
            	return $queryFirst->whereHas('employeeStore.store', function($query) use ($value){ 
	                return $query->where('id', $value);
	            })->orWhereHas('attendance.attendanceDetail.store', function($query) use ($value){ 
	                return $query->where('id', $value);
	            });
            })            
            : null;
    }

    public function id_emp($value) {
        return (!$this->requestAllData($value)) ? $this->builder->where('employees.id', $value) : null;
    }

    public function id_role($value = '') {
        return ((!$this->requestAllData($value)) && ($value != '')) ?
		$this->builder->whereHas('position', function($q) use ($value){
			return $q->where('level', $value);
		})
		: null;
    }

    // public function newPeriode($value = '') {
    // 	if(!$this->requestAllData($value)){
    // 		return $this->builder->whereHas('attendance', function($query) use ($value){ 
    //             return $query->whereMonth('date', '=', Carbon::parse($value)->format('m'))
				// 			 ->whereYear('date', '=', Carbon::parse($value)->format('Y'));
    //         });
    // 	}else{
    // 		return null;
    // 	}
    // }
}