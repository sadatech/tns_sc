<?php

namespace App\Filters;

use Illuminate\Http\Request;

class AttendanceFilters extends QueryFilters
{
	public function periode($value) {
		return (!$this->requestAllData($value)) ?
		$this->builder->whereMonth('checkin', substr($value, 0, 2));
		$this->builder->whereYear('checkin', substr($value, 3));
		: null;
	}

	public function employee($value)
	{
		return (!$this->requestAllData($value)) ?
		$this->builder->whereHas('attendance.employee', function($q) use ($request){
			return $q->where('id_employee', $value);
		});
		: null;
	}
}