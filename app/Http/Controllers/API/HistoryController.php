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
use App\NewCbd;
use App\PlanDc;
use App\DocumentationDc;
use App\DisplayShare;
use App\DetailDisplayShare;
use App\AdditionalDisplay;
use App\DetailAdditionalDisplay;
use App\Availability;
use App\DetailAvailability;
use App\Promo;
use App\PromoDetail;
use App\Oos;
use App\OosDetail;
use App\DataPrice;
use App\DetailDataPrice;
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
				$month 	= $now->month-1;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get();

			if ($header->count() > 0) {
				$dataArr = array();
				foreach ($header as $key => $head) {
					if (strtoupper($type) == 'MTC') {
						$detail = AttendanceDetail::where('id_attendance',$head->id)->with('attendance.employee.timezone')->get();
					}else if( strtoupper($type) == 'GTC-MD'  ){
						$detail = AttendanceOutlet::where('id_attendance',$head->id)->with('attendance.employee.timezone')->get();
					}else if( strtoupper($type) == 'GTC-SPG'  ){
						$detail = AttendancePasar::where('id_attendance',$head->id)->with('attendance.employee.timezone')->get();
					}else if( strtoupper($type) == 'GTC-DC'  ){
						$detail = AttendancePlace::where('id_attendance',$head->id)->with('attendance.employee.timezone')->get();
					}else if( strtoupper($type) == 'GTC-MOTORIC'  ){
						$detail = AttendanceBlock::where('id_attendance',$head->id)->with('attendance.employee.timezone')->get();
					}

					foreach ($detail as $key2 => $value2) {//timezone
						$detail[$key2]['checkin_old'] 	= $detail[$key2]['checkin'];
						$detail[$key2]['checkout_old'] 	= $detail[$key2]['checkout'];

						$detail[$key2]['checkin'] 	= Carbon::parse($value2->checkin)->setTimezone($value2->attendance->employee->timezone->timezone)->format('Y-m-d H:i:s');
						$detail[$key2]['checkout'] 	= ($value2->checkout ? Carbon::parse($value2->checkout)->setTimezone($value2->attendance->employee->timezone->timezone)->format('Y-m-d H:i:s') : "-");
						
						unset($detail[$key2]['attendance']);
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

	public function salesHistory($type='MTC', $date = '', $sales = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

			if (strtoupper($type) == 'MTC') {
				$header = Sales::query();
				$header->whereType('Sell In');
			}else if (strtoupper($type) == 'MTC-O') {
				$header = Sales::query();
				$header->whereType('Sell Out');
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
						'icip_icip' 			=> $head->icip_icip ?? '',
						'effevtive_contact' 	=> $head->effevtive_contact ?? '',
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

	public function distributionHistory($type = 'MD', $date = '')
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

	public function newCbdHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= NewCbd::where('id_employee', $user->id)
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

	public function displayShareHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= DisplayShare::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get()->toArray();

			foreach ($data as $key => $value) {
				$data[$key]['detail'] = DetailDisplayShare::whereIdDisplayShare($value['id'])->get();
			}

			if (count($data) > 0) {
				$res['success'] 		= true;
				$res['data'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Display Share not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

	public function additionalDisplayHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= AdditionalDisplay::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get()->toArray();

			foreach ($data as $key => $value) {
			$data[$key]['detail'] = DetailAdditionalDisplay::whereIdAdditionalDisplay($value['id'])->get();
			}

			if (count($data) > 0) {
				$res['success'] 		= true;
				$res['data'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Additional Display not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

	public function availabilityHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= Availability::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get()->toArray();

			foreach ($data as $key => $value) {
				$data[$key]['detail'] = DetailAvailability::whereIdAvailability($value['id'])->get();
			}

			if (count($data) > 0) {
				$res['success'] = true;
				$res['data'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Availability not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

	public function promoHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= Promo::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('created_at', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('created_at', $month)->whereYear('created_at', $year);
			})->orderBy('id','desc')->get()->toArray();

			foreach ($data as $key => $value) {
				$data[$key]['detail'] = PromoDetail::whereIdPromo($value['id'])->get();
			}

			if (count($data) > 0) {
				$res['success'] 		= true;
				$res['data'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Promo not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

	public function stockHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= Oos::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get()->toArray();

			foreach ($data as $key => $value) {
				$data[$key]['detail'] = OosDetail::whereIdOos($value['id'])->get();
			}

			if (count($data) > 0) {
				$res['success'] 		= true;
				$res['data'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Stock not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

	public function priceHistory($date = '')
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data 	= DataPrice::where('id_employee', $user->id)
			->when($date != '', function ($q) use ($date){
				return $q->whereDate('date', $date);
			})
			->when($date == '', function ($q){
				$now 	= Carbon::now();
				$year 	= $now->year;
				$month 	= $now->month;
				return $q->whereMonth('date', $month)->whereYear('date', $year);
			})->orderBy('id','desc')->get()->toArray();

			foreach ($data as $key => $value) {
				$data[$key]['detail'] = DetailDataPrice::whereIdDataPrice($value['id'])->get();
			}

			if (count($data) > 0) {
				$res['success'] 		= true;
				$res['data'] 	= $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Price not Found.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res);
	}

}