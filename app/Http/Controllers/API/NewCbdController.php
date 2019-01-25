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
}