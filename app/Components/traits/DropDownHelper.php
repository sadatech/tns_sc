<?php

namespace App\Components\traits;

/**
 * 
 */

trait DropDownHelper
{

	public static function toDropDownData($where = null, $value = 'id', $label = 'name', $orderBy = ['id', 'asc']){
		$orderBy[1] = isset($orderBy[1]) ? $orderBy[1] : 'asc';
		$model = $where ? static::where($where[0], $where[1])->orderBy($orderBy[0], $orderBy[1])->get() : static::orderBy($orderBy[0], $orderBy[1])->get();

		$results = [];
		foreach ($model as $key => $row) {
			$results[$row->$value] = $row->$label;
		}

		return $results;
	}
	
}