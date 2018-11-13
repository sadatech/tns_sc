<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Attendance;
use App\AttendanceDetail;
use App\AttendanceOutlet;
use Carbon\Carbon;
use Config;
use JWTAuth;

class HistoryController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function attenadnceHistory($type='MTC', $date = '')
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					if ($type == 'MTC') {
						$hasDetail = 'attendanceDetail';
					}else{
						$hasDetail = 'attendanceOutlet';
					}
					$header = Attendance::whereHas($hasDetail, function($query) use ($date)
					{
						if ($date == '') {
							$now 	= Carbon::now();
							$year 	= $now->year;
							$month 	= $now->month;
							return $query->whereMonth('date', $month)->whereYear('date', $year);
						}else
						return $query->whereDate('date', $date);
					})->where('id_employee', $user->id)->get();

					if ($header->count() > 0) {
						$dataArr = array();
						foreach ($header as $key => $head) {
							if ($type == 'MTC') {
								$detail = AttendanceDetail::where('id_attendance',$head->id)->get();
							}else{
								$detail = AttendanceOutlet::where('id_attendance',$head->id)->get();
							}
							$dataArr[] = array(
								'id' 			=> $head->id,
								'id_employee' 	=> $head->id_employee,
								'date' 			=> $head->date,
								'keterangan' 	=> $head->keterangan,
								'detail' 		=> $detail,
							);
						}
						$res['success'] = true;
						$res['attendance'] = $dataArr;
					} else {
						$res['msg'] 	= "Attendance not Found.";
					}
				}
			}else{
				$res['msg'] = "User not found.";
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
		}
		return response()->json($res);
	}
	
}