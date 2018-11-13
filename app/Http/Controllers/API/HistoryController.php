<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\Attendance;
use App\AttendanceDetail;
use App\AttendanceOutlet;
use App\Sales;
use App\DetailSales;
use App\SalesMd;
use App\SalesMdDetail;
use App\StockMdHeader;
use App\StockMdDetail;
use Carbon\Carbon;
use Config;
use JWTAuth;

class HistoryController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function attenadnceHistory($type='MTC', $date = '')
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {
			$user = $check['user'];
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
		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res);
	}

	public function salesHistory($type='MTC', $date = '')
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {
			$user = $check['user'];
			if ($type == 'MTC') {
				$header = Sales::where('id_employee', $user->id)->whereHas('DetailSales', function($query) use ($date)
				{
					if ($date == '') {
						$now 	= Carbon::now();
						$year 	= $now->year;
						$month 	= $now->month;
						return $query->whereMonth('date', $month)->whereYear('date', $year);
					}else
					return $query->whereDate('date', $date);
				})->get();
			}else{
				$header = SalesMd::where('id_employee', $user->id)->whereHas('DetailSales', function($query) use ($date)
				{
					if ($date == '') {
						$now 	= Carbon::now();
						$year 	= $now->year;
						$month 	= $now->month;
						return $query->whereMonth('date', $month)->whereYear('date', $year);
					}else
					return $query->whereDate('date', $date);
				})->get();
			}

			if ($header->count() > 0) {
				$dataArr = array();
				foreach ($header as $key => $head) {
					if ($type == 'MTC') {
						$detail = DetailSales::where('id_sales',$head->id)->get();
					}else{
						$detail = SalesMdDetail::where('id_sales',$head->id)->get();
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
				$res['sales'] = $dataArr;
			} else {
				$res['msg'] 	= "Sales not Found.";
			}
		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res);
	}

	public function stockistHistory($date = '')
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {
			$user = $check['user'];
			
				$header = StockMdHeader::where('id_employee', $user->id)->whereHas('stockDetail', function($query) use ($date)
				{
					if ($date == '') {
						$now 	= Carbon::now();
						$year 	= $now->year;
						$month 	= $now->month;
						return $query->whereMonth('date', $month)->whereYear('date', $year);
					}else
					return $query->whereDate('date', $date);
				})->get();

			if ($header->count() > 0) {
				$dataArr = array();
				foreach ($header as $key => $head) {
					
						$detail = StockMdDetail::where('id_stock',$head->id)->get();
					
					$dataArr[] = array(
						'id' 			=> $head->id,
						'id_employee' 	=> $head->id_employee,
						'date' 			=> $head->date,
						'keterangan' 	=> $head->keterangan,
						'detail' 		=> $detail,
					);
				}
				$res['success'] = true;
				$res['stockist'] = $dataArr;
			} else {
				$res['msg'] 	= "Stockist not Found.";
			}
		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res);
	}
	
}