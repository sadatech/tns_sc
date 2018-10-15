<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Place;
use JWTAuth;
use Config;

class PlaceController extends Controller
{
	public function list()
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
			} else {
				$place = Place::get();
				if (!$place->isEmpty()) {
					$placeArr = array();
					foreach ($place as $key => $value) {
						$placeArr[$key] = array(
							'id' => $value->id,
							'code' => $value->code,
							'name' => $value->name,
							'city' => $value->city->name,
							'province' => $value->province->name,
							'phone' => $value->phone,
							'email' => $value->email,
							'latitude' => $value->latitude,
							'longitude' => $value->longitude,
							'address' => $value->address,
							'description' => $value->description,
						);
					}
					$res['success'] = true;
					$res['place'] = $placeArr;
				} else {
					$res['success'] = false;
					$res['msg'] = "Gagal mencari place.";
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
