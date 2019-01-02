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

class NewCbdController extends Controller
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
			if ($image 	= $request->file('photo')) {
				$photo 	= time()."_".$image->getClientOriginalName();
				$path 	= 'uploads/cbd';
				$image->move($path, $photo);
				$image_compress = Image::make($path.'/'.$photo)->orientate();
				$image_compress->save($path.'/'.$photo, 50);
			}
			$insert = NewCbd::create([
				'id_employee'			=> $user->id,
				'id_outlet'				=> $request->outlet,
				'date'					=> Carbon::today()->toDateString(),
				'photo'					=> $photo,
				'posm_shop_sign'		=> $request->posm_shop_sign,
				'posm_others'			=> $request->posm_others,
				'posm_hangering_mobile'	=> $request->posm_hangering_mobile,
				'posm_poster'			=> $request->posm_poster,
				'cbd_competitor_detail'	=> $request->cbd_competitor_detail,
				'cbd_competitor'		=> $request->cbd_competitor,
				'cbd_position'			=> $request->cbd_position,
				'outlet_type'			=> $request->outlet_type,
				'total_hanger'			=> $request->total_hanger,
			]);
			if ($insert->id) {
				$res['success'] = true;
				$res['msg'] 	= "Success add CBD.";
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Fail add CBD.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}