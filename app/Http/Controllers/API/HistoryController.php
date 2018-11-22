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
use App\SalesSpgPasar;
use App\SalesSpgPasarDetail;
use App\SalesRecap;
use App\StockMdHeader;
use App\StockMdDetail;
use App\Distribution;
use App\DistributionDetail;
use App\Cbd;
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

		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

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
					if (strtoupper($type) == 'MTC') {
						$detail = AttendanceDetail::where('id_attendance',$head->id)->get();
					}else if( strtoupper($type) == 'GTC-MD'  ){
						$detail = AttendanceOutlet::where('id_attendance',$head->id)->get();
					}else if( strtoupper($type) == 'GTC-SPG'  ){
						$detail = AttendancePasar::where('id_attendance',$head->id)->get();
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
				$res['success'] = false;
				$res['msg'] 	= "Attendance not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		
		return response()->json($res, $code);
	}

	public function salesHistory($type='MTC', $date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

			if (strtoupper($type) == 'MTC') {
				$header = Sales::query();
			}else if (strtoupper($type) == 'GTC-MD') {
				$header = SalesMd::query();
			}else if (strtoupper($type) == 'GTC-SPG') {
				$header = SalesSpgPasar::query();
			}

			$header->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			});
			$header->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			});

			if ($header->get()->count() > 0) {
				$dataArr = array();
				foreach ($header->get() as $key => $head) {
					if ($type == 'MTC') {
						$detail = DetailSales::query();
					}else if (strtoupper($type) == 'GTC-MD') {
						$detail = SalesMdDetail::query();
					}else if (strtoupper($type) == 'GTC-SPG') {
						$detail = SalesSpgPasar::query();
					}
					$detail->where('id_sales',$head->id);
					$dataArr[] = array(
						'id' 			=> $head->id,
						'id_employee' 	=> $head->id_employee,
						'date' 			=> $head->date,
						'keterangan' 	=> $head->keterangan,
						'detail' 		=> $detail->get(),
					);
				}
				$res['success'] = true;
				$res['sales'] = $dataArr;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Sales not Found.";
			}
		}else{
			$res = $check;
		}

			$code = $res['code'];
			unset($res['code']);
		
		return response()->json($res);
	}

	public function salesRecapHistory($date = '')
	{
		$check = $this->authCheck();

		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;
			
			$header = SalesRecap::query();

			$header->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			});
			$header->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			});

			if ($header->get()->count() > 0) {
				$dataArr = array();
				foreach ($header->get() as $key => $data) {
					$dataArr[] = array(
						'id' 			=> $data->id,
						'id_employee'	=> $data->id_employee,
						'id_outlet'		=> $data->id_outlet,
						'date'			=> $data->date,
						'total_buyer'	=> $data->total_buyer,
						'total_sales'	=> $data->total_buyer,
						'total_value'	=> $data->total_buyer,
						'photo' 		=> $data->photo,
					);
				}
				$res['success'] = true;
				$res['sales'] = $dataArr;
			} else {
				$res['msg'] 	= "Sales recap not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		
		return response()->json($res);
	}

	public function stockistHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

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
				$res['success'] = false;
				$res['msg'] 	= "Stockist not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		
		return response()->json($res);
	}

	public function distributionHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;
			
			$header = Distribution::where('id_employee', $user->id)->whereHas('distributionDetail', function($query) use ($date)
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
					
					$detail = DistributionDetail::where('id_distribution',$head->id)->get();
					
					$dataArr[] = array(
						'id' 			=> $head->id,
						'id_employee' 	=> $head->id_employee,
						'date' 			=> $head->date,
						'keterangan' 	=> $head->keterangan,
						'detail' 		=> $detail,
					);
				}
				$res['success'] = true;
				$res['distribution'] = $dataArr;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Distribution not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		
		return response()->json($res);
	}
	
	public function cbdHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

			$data 	= Cbd::where(function($query) use ($date)
			{
				if ($date == '') {
					$now 	= Carbon::now();
					$year 	= $now->year;
					$month 	= $now->month;
					return $query->whereMonth('date', $month)->whereYear('date', $year);
				}else
				return $query->whereDate('date', $date);
			})->get();
			if ($data->count() > 0) {
				$res['success'] = true;
				$res['cbd'] = $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "CBD not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		
		return response()->json($res);
	}
}