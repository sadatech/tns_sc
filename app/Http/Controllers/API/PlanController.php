<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\PlanDc;
use App\PlanEmployee;
use Config;
use JWTAuth;
use Carbon\Carbon;

class PlanController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function update(Request $request)
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {
			
			if ($image 	= $request->file('photo')) {
				$photo 	= time()."_".$image->getClientOriginalName();
				$path 	= 'uploads/plan';
				$image->move($path, $photo);
			}
			$date 	= Carbon::now()->toDateString();
			$user = $check['user'];

			$update 	= PlanDc::whereDate('date',$date)->whereHas('planEmployee', function($query) use ($user)
			{
				return $query->where('id_employee', $user->id);
			})->orderBy('id','desc')
			->update([
				'stocklist'	=> $request->stocklist,
				'actual'		=> $request->actual,
				'photo'			=> $photo,
			]);

			if ($update) {
				$res['success'] = true;
				$res['msg'] 	= "Success Checkin.";
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Fail Checkin.";
			}

		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res, $code);
	}

	public function date()
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {

			$date 	= Carbon::now()->toDateString();
			$user = $check['user'];

			$data 	= PlanDc::whereDate('date',$date)->whereHas('planEmployee', function($query) use ($user)
			{
				return $query->where('id_employee', $user->id);
			})->orderBy('id','desc')->first();

			if (isset($data)) {
				$res['success'] = true;
				$res['plan'] = $data->plan;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Gagal mengambil Plan.";
			}

		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res, $code);
	}

	public function month()
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {

			$now 	= Carbon::now();
			$year 	= $now->year;
			$month 	= $now->month;
			$user = $check['user'];

			$data 	= PlanDc::whereMonth('date', $month)->whereYear('date', $year)
			->whereHas('planEmployee', function($query) use ($user)
			{
				return $query->where('id_employee', $user->id);
			})->orderBy('id','desc')->get();

			if ($data->count() > 0) {
				$res['success'] = true;
				$res['plan'] = $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Gagal mengambil Plan.";
			}

		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res, $code);
	}
}