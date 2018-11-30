<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\Attendance;
use App\AttendanceDetail;
use App\AttendanceOutlet;
use App\AttendancePasar;
use App\AttendancePlace;
use App\AttendanceBlock;
use App\Sales;
use App\DetailSales;
use App\SalesMd;
use App\SalesMdDetail;
use App\SalesSpgPasar;
use App\SalesSpgPasarDetail;
use App\SalesRecap;
use App\SamplingDc;
use App\SamplingDcDetail;
use App\SalesDc;
use App\SalesDcDetail;
use App\SalesMotoric;
use App\SalesMotoricDetail;
use App\StockMdHeader;
use App\StockMdDetail;
use App\Distribution;
use App\DistributionDetail;
use App\DistributionMotoric;
use App\DistributionMotoricDetail;
use App\Cbd;
use App\PlanDc;
use App\DocumentationDc;
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

			$header = Attendance::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get();

			if ($header->count() > 0) {
				$dataArr = array();
				foreach ($header as $key => $head) {
					if (strtoupper($type) == 'MTC') {
						$detail = AttendanceDetail::where('id_attendance',$head->id)->get();
					}else if( strtoupper($type) == 'GTC-MD'  ){
						$detail = AttendanceOutlet::where('id_attendance',$head->id)->get();
					}else if( strtoupper($type) == 'GTC-SPG'  ){
						$detail = AttendancePasar::where('id_attendance',$head->id)->get();
					}else if( strtoupper($type) == 'GTC-DC'  ){
						$detail = AttendancePlace::where('id_attendance',$head->id)->get();
					}else if( strtoupper($type) == 'GTC-MOTORIC'  ){
						$detail = AttendanceBlock::where('id_attendance',$head->id)->get();
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
			}else if (strtoupper($type) == 'GTC-DC') {
				$header = SalesDc::query();
			}else if (strtoupper($type) == 'GTC-SAMPLING') {
				$header = SamplingDc::query();
			}else if (strtoupper($type) == 'GTC-MOTORIC') {
				$header = SalesMotoric::query();
			}

			$header->where('id_employee', $user->id)
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})->orderBy('id','desc');

			if ($header->get()->count() > 0) {
				$dataArr = array();
				foreach ($header->get() as $key => $head) {
					if ($type == 'MTC') {
						$detail = DetailSales::query();
					}else if (strtoupper($type) == 'GTC-MD') {
						$detail = SalesMdDetail::query();
					}else if (strtoupper($type) == 'GTC-SPG') {
						$detail = SalesSpgPasarDetail::query();
					}else if (strtoupper($type) == 'GTC-DC') {
						$detail = SalesDcDetail::query();
					}else if (strtoupper($type) == 'GTC-SAMPLING') {
						$detail = SamplingDcDetail::query();
					}else if (strtoupper($type) == 'GTC-MOTORIC') {
						$detail = SalesMotoricDetail::query();
					}

					$detail->where('id_sales',$head->id);
					$dataArr[] = array(
						'id' 			=> $head->id,
						'id_employee' 	=> $head->id_employee,
						'id_store' 		=> $head->id_store ?? '',
						'store_name'	=> $head->store->name1 ?? '',
						'id_outlet' 	=> $head->id_outlet ?? '',
						'outlet_name' 	=> $head->outlet->name ?? '',
						'outlet_pasar_name' 	=> $head->outlet->employeePasar->pasar->name ?? '',
						'id_pasar' 		=> $head->id_pasar ?? '',
						'pasar_name' 	=> $head->pasar->name ?? '',
						'name_for_spg' 	=> $head->name ?? '',
						'phone_for_spg' => $head->phone ?? '',
						'place' 		=> $head->place ?? '',
						'id_block' 		=> $head->id_block ?? '',
						'block_name' 	=> $head->block->name ?? '',
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
			
			$header = SalesRecap::when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})->where('id_employee', $user->id)->orderBy('id','desc');

			if ($header->get()->count() > 0) {
				$dataArr = array();
				foreach ($header->get() as $key => $data) {
					$dataArr[] = array(
						'id' 			=> $data->id,
						'id_employee'	=> $data->id_employee,
						'id_outlet'		=> $data->id_outlet,
						'outlet_name'	=> $data->outlet->name ?? '',
						'pasar_name'	=> $data->outlet->employeePasar->pasar->name ?? '',
						'date'			=> $data->date,
						'total_buyer'	=> $data->total_buyer,
						'total_sales'	=> $data->total_buyer,
						'total_value'	=> $data->total_buyer,
						'photo' 		=> $data->photo,
						'photo_url' 	=> asset('uploads/sales_recap/'.$data->photo),
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

			$header = StockMdHeader::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get();

			if ($header->count() > 0) {
				$dataArr = array();
				foreach ($header as $key => $head) {
					
					$detail = StockMdDetail::where('id_stock',$head->id)->get();
					
					$dataArr[] = array(
						'id' 			=> $head->id,
						'id_employee' 	=> $head->id_employee,
						'date' 			=> $head->date,
						'stockist' 		=> $head->stockist,
						'pasar' 		=> $head->pasar->name,
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

	public function distributionHistory($date = '', $type = 'MD')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;
			
			if (strtoupper($type) == 'MOTORIC') {
				$header = DistributionMotoric::query();
				$detailTemplate = new DistributionMotoricDetail;
			}else{
				$header = Distribution::query();
				$detailTemplate = new DistributionDetail;
			}

			$header = $header->where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get();

			if ($header->count() > 0) {
				$dataArr = array();
				foreach ($header as $key => $head) {

					$detail = $detailTemplate;
					$detail = $detail->where('id_distribution',$head->id)->get();

					$dataArr[] = array(
						'id' 			=> $head->id,
						'id_employee' 	=> $head->id_employee,
						'id_outlet' 	=> $head->id_outlet ?? '',
						'outlet_name' 	=> $head->outlet->name ?? '',
						'outlet_pasar_name' 	=> $head->outlet->employeePasar->pasar->name ?? '',
						'id_pasar' 		=> $head->id_pasar ?? '',
						'pasar_name' 	=> $head->pasar->name ?? '',
						'id_block' 		=> $head->id_block ?? '',
						'block_name' 	=> $head->block->name ?? '',
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

			$data 	= Cbd::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get();

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

	public function dcHistory($type='SALES', $date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			if (strtoupper($type) == 'SALES') {
				$header = SalesDc::query();
			}else if (strtoupper($type) == 'SAMPLING') {
				$header = SamplingDc::query();
			}

			$header->where('id_employee', $user->id)
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})->orderBy('id','desc');

			if ($header->get()->count() > 0) {
				$dataArr = array();
				foreach ($header->get() as $key => $head) {
					if (strtoupper($type) == 'SALES') {
						$detail = SalesDcDetail::query();
					}else if (strtoupper($type) == 'SAMPLING') {
						$detail = SamplingDcDetail::query();
					}else{
						$res['success'] = false;
						$res['msg'] 	= "$type not Found.";
						return response()->json($res);
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
				$res['msg'] 	= "$type not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

	public function planHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= PlanDc::whereHas('PlanEmployee', function($q) use ($user)
			{
				return $q->where('id_employee', $user->id);
			})
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get();

			if ($data->count() > 0) {
				$res['success'] = true;
				$res['plan'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Plan not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

	public function documentationHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= DocumentationDc::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get();

			if ($data->count() > 0) {
				$res['success'] 		= true;
				$res['documentation'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Documentation not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

}