<?php

namespace App\Components\traits;

use Carbon\Carbon;

trait WeekHelper
{

	public static function getWeek($date){
		$week = Carbon::parse($date)->weekOfMonth;
		return ($week == '5') ? '4' : $week;
		// $day = Carbon::parse($date)->day;
		// switch ($day) {
		// 	case $day <= 7:
		// 		$week = 1;
		// 		break;
		// 	case $day <= 14:
		// 		$week = 2;
		// 		break;
		// 	case $day <= 21:
		// 		$week = 3;
		// 		break;
		// 	case $day <= 31:
		// 		$week = 4;
		// 		break;
			
		// 	default:
		// 		$week = 1;
		// 		break;
		// }
		// return $week;
	}
	
}