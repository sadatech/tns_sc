<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\HeaderIn;
use App\HeaderOut;
use App\DetailIn;
use App\DetailOut;
use Carbon;

class SellController extends Controller
{
	public function store($type)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
			} else {		
				$date = Carbon::parse($request->input('date'));
				if ($type == 1) {
					$checkHeader = HeaderIn::whereMonth('date', $date)->count();
					HeaderIn::create([
						'id_employee' => $user->id,
						// 'id_store' => ,
						'date' => $date,
						'week' => $date->weekOfMonth,
					]);
				} else if ($type == 2) {

				} else if ($type == 3) {

				}
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
