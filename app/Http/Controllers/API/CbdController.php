<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Config;
use JWTAuth;
use Image;
use App\Cbd;

class CbdController extends Controller
{
    public function __construct()
    {
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list()
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					$cbd 	= Cbd::get();
					if ($cbd->count() > 0) {
						$res['success'] = true;
						$res['cbd'] = $cbd;
					} else {
						$res['msg'] 	= "Gagal mengambil CBD.";
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

	public function store(Request $request)
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					$code 	= 200;
					if ($image 	= $request->file('photo')) {
						$photo 	= time()."_".$image->getClientOriginalName();
						$path 	= 'uploads/cbd';
						$image->move($path, $photo);
						$image_compress = Image::make($path.'/'.$photo)->orientate();
						$image_compress->save($path.'/'.$photo, 50);
					}
					$insert = CBD::create([
						'id_employee'	=> $user->id,
						'id_outlet'		=> $request->outlet,
						'date'			=> Carbon::today()->toDateString(),
						'photo'			=> $photo,
					]);
					if ($insert->id) {
						$res['success'] = true;
						$res['msg'] 	= "Success add CBD.";
					} else {
						$res['success'] = false;
						$res['msg'] 	= "Fail add CBD.";
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
