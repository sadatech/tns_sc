<?php

namespace App\Http\Controllers\API;

use App\Components\traits\ApiAuthHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Config;
use JWTAuth;
use Image;
use App\NewCbd;
use App\Outlet;
use App\Employee;
use App\TargetGtc;

class DashboardController extends Controller
{
	use ApiAuthHelper;
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list()
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;
			$cbd 	= NewCbd::get();
			if ($cbd->count() > 0) {
				$res['success'] = true;
				$res['cbd'] = $cbd;
			} else {
				$res['msg'] 	= "Gagal mengambil CBD.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function store(Request $request)
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;

			if (Outlet::whereId($request->outlet)->get()->count() > 0) {
				if ($image 	= $request->file('photo')) {
					$photo 	= time()."_".$image->getClientOriginalName();
					$path 	= 'uploads/cbd';
					$image->move($path, $photo);
					$image_compress = Image::make($path.'/'.$photo)->orientate();
					$image_compress->save($path.'/'.$photo, 50);
				}

				if ($image 	= $request->file('photo2')) {
					$photo2 	= time()."_".$image->getClientOriginalName();
					$path 	= 'uploads/cbd';
					$image->move($path, $photo2);
					$image_compress = Image::make($path.'/'.$photo2)->orientate();
					$image_compress->save($path.'/'.$photo2, 50);
				}

				// $insert = NewCbd::create([
				// 	'id_employee'			=> $user->id,
				// 	'id_outlet'				=> $request->outlet,
				// 	'date'					=> Carbon::today()->toDateString(),
				// 	'photo'					=> $photo?? null,
				// 	'photo2'				=> $photo2?? null,
				// 	'posm_shop_sign'		=> $request->posm_shop_sign?? null,
				// 	'posm_others'			=> $request->posm_others?? null,
				// 	'posm_hangering_mobile'	=> $request->posm_hangering_mobile?? null,
				// 	'posm_poster'			=> $request->posm_poster?? null,
				// 	'cbd_competitor'		=> $request->cbd_competitor,
				// 	'cbd_position'			=> $request->cbd_position,
				// 	'outlet_type'			=> $request->outlet_type,
				// 	'total_hanger'			=> $request->total_hanger,
				// ]);

		        $cbd = NewCbd::where('id_employee', $user->id)
		            ->where('id_outlet', $request->outlet)
					->whereMonth('date', Carbon::now()->month)
		            ->whereYear('date', Carbon::now()->year)
		            ->where('approve', 1)->first();

		        if ($cbd) {
					$insert = NewCbd::create([
						'id_employee'			=> $user->id,
						'id_outlet'				=> $request->outlet,
						'date'					=> Carbon::today()->toDateString(),
						'photo'					=> $photo?? null,
						'photo2'				=> $photo2?? null,
						'posm_shop_sign'		=> $request->posm_shop_sign?? null,
						'posm_others'			=> $request->posm_others?? null,
						'posm_hangering_mobile'	=> $request->posm_hangering_mobile?? null,
						'posm_poster'			=> $request->posm_poster?? null,
						'cbd_competitor'		=> $request->cbd_competitor,
						'cbd_position'			=> $request->cbd_position,
						'outlet_type'			=> $request->outlet_type,
						'total_hanger'			=> $request->total_hanger,
						'propose'				=> 0,
						'approve'				=> 1,
						'reject'				=> 0,
					]);
		        }else{
					$insert = NewCbd::create([
						'id_employee'			=> $user->id,
						'id_outlet'				=> $request->outlet,
						'date'					=> Carbon::today()->toDateString(),
						'photo'					=> $photo?? null,
						'photo2'				=> $photo2?? null,
						'posm_shop_sign'		=> $request->posm_shop_sign?? null,
						'posm_others'			=> $request->posm_others?? null,
						'posm_hangering_mobile'	=> $request->posm_hangering_mobile?? null,
						'posm_poster'			=> $request->posm_poster?? null,
						'cbd_competitor'		=> $request->cbd_competitor,
						'cbd_position'			=> $request->cbd_position,
						'outlet_type'			=> $request->outlet_type,
						'total_hanger'			=> $request->total_hanger,
					]);
		        }
				if ($insert->id) {
					$res['success'] = true;
					$res['msg'] 	= "Success add CBD.";
				} else {
					$res['success'] = false;
					$res['msg'] 	= "Fail add CBD.";
				}
			}else{
				$res['success'] = false;
				$res['msg'] 	= "Fail Outlet not found.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function CbdByEmployee()
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;

	        $periode = Carbon::now();
	        // $periode = Carbon::parse('January 2019');

	        $target = TargetGtc::where('id_employee', $user->id)
	                        ->whereMonth('rilis', $periode->month)
	                        ->whereYear('rilis', $periode->year)
	                        ->orderBy('rilis', 'DESC')
	                        ->groupBy('id_employee')
	                        ->get();
	        $data['cbd_target'] = $target->sum('cbd');
	        $data['cbd_actual'] = NewCbd::whereMonth('date', $periode->month)
	                        ->whereYear('date', $periode->year)
	                        ->where('id_employee', $user->id)
	                        ->where('reject','!=',1)
	                        ->groupBy('id_outlet')
	                        ->get()->count('id_outlet');
	        $data['cbd_less'] = $data['cbd_target']-$data['cbd_actual'];
	        if ($data['cbd_less'] <= 0) {
	            $data['cbd_less'] = 0;
	        }
	        $persenAkual = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_actual']/$data['cbd_target']*100),2).'%');
	        $persenGap = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_less']/$data['cbd_target']*100),2).'%');

	        $cbd = array();
	        $id = 1;
	        $cbd[] = array(
	            'id'        => $id++,
	            'name'      => 'CBD Aktual',
	            'value'      => $data['cbd_actual'],
	            'persen'	=> $persenAkual,
	        );
	        $cbd[] = array(
	            'id'        => $id++,
	            'name'      => 'GAP',
	            'value'      => $data['cbd_less'],
	            'persen'	=> $persenGap,
	        );

			if (count($cbd) > 0) {
				$res['success'] = true;
				$res['cbd'] = $cbd;
			} else {
				$res['msg'] 	= "Gagal mengambil Data.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function CbdDetail()
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;

	        $periode = Carbon::now();
	        // $periode = Carbon::parse('January 2019');

	        $target = TargetGtc::where('id_employee', $user->id)
	                        ->whereMonth('rilis', $periode->month)
	                        ->whereYear('rilis', $periode->year)
	                        ->orderBy('rilis', 'DESC')
	                        ->groupBy('id_employee')
	                        ->get();
	        $data['cbd_target'] = $target->sum('cbd');
	        $data['cbd_actual'] = NewCbd::whereMonth('date', $periode->month)
	                        ->whereYear('date', $periode->year)
	                        ->where('id_employee', $user->id)
	                        ->where('reject','!=',1)
	                        ->groupBy('id_outlet')
	                        ->get()->count('id_outlet');
	        $data['cbd_less'] = $data['cbd_target']-$data['cbd_actual'];
	        if ($data['cbd_less'] <= 0) {
	            $data['cbd_less'] = 0;
	        }
	        $data['cbd_propose'] = NewCbd::whereMonth('date', $periode->month)
	                        ->whereYear('date', $periode->year)
	                        ->where('id_employee', $user->id)
	                        ->where('propose','=',1)
	                        ->groupBy('id_outlet')
	                        ->get()->count('id_outlet');
	        $data['cbd_approve'] = NewCbd::whereMonth('date', $periode->month)
	                        ->whereYear('date', $periode->year)
	                        ->where('id_employee', $user->id)
	                        ->where('approve','=',1)
	                        ->groupBy('id_outlet')
	                        ->get()->count('id_outlet');
	        $data['cbd_reject'] = NewCbd::whereMonth('date', $periode->month)
	                        ->whereYear('date', $periode->year)
	                        ->where('id_employee', $user->id)
	                        ->where('reject','=',1)
	                        ->groupBy('id_outlet')
	                        ->get()->count('id_outlet');

	        $persenAkual = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_actual']/$data['cbd_target']*100),2).'%');
	        $persenGap = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_less']/$data['cbd_target']*100),2).'%');
	        $persenPropose = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_propose']/$data['cbd_target']*100),2).'%');
	        $persenApprove = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_approve']/$data['cbd_target']*100),2).'%');
	        $persenReject = ($data['cbd_target'] == 0) ? '0 %' : (round(($data['cbd_reject']/$data['cbd_target']*100),2).'%');

	        $cbd = array();
	        $id = 1;
	        $cbd[] = array(
	            'id'        => $id++,
	            'name'      => 'GAP',
	            'value'      => $data['cbd_less'],
	            'persen'	=> $persenGap,
	        );
	        $cbd[] = array(
	            'id'        => $id++,
	            'name'      => 'CBD Aktual',
	            'value'      => $data['cbd_actual'],
	            'persen'	=> $persenAkual,
	        );
	        $cbd[] = array(
	            'id'        => $id++,
	            'name'      => 'CBD propose',
	            'value'      => $data['cbd_propose'],
	            'persen'	=> $persenPropose,
	        );
	        $cbd[] = array(
	            'id'        => $id++,
	            'name'      => 'CBD approve',
	            'value'      => $data['cbd_approve'],
	            'persen'	=> $persenApprove,
	        );
	        $cbd[] = array(
	            'id'        => $id++,
	            'name'      => 'CBD reject',
	            'value'      => $data['cbd_reject'],
	            'persen'	=> $persenReject,
	        );

			if (count($cbd) > 0) {
				$res['success'] = true;
				$res['cbd'] = $cbd;
			} else {
				$res['msg'] 	= "Gagal mengambil Data.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function CbdReject()
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;

	        $periode = Carbon::now();
	        // $periode = Carbon::parse('January 2019');

	        $data = NewCbd::whereMonth('date', $periode->month)
	                        ->whereYear('date', $periode->year)
	                        ->where('id_employee', $user->id)
	                        ->where('reject','=',1)
	                        ->groupBy('id_outlet')
	                        ->get();

			if (count($data) > 0) {
				$res['success'] = true;
				$res['CbdReject'] = $data;
			} else {
				$res['msg'] 	= "Gagal mengambil Data.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}