<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Place;
use JWTAuth;
use Config;

class PlaceController extends Controller
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
					$place = Place::get();
					if (!$place->isEmpty()) {
						$placeArr = array();
						foreach ($place as $key => $value) {
							$placeArr[$key] = array(
								'id' => $value->id,
								'code' => $value->code,
								'name' => $value->name,
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
						$res['msg'] = "Place not found.";
					}
					$res['success'] = true;
					$res['place'] = $placeArr;
				} else {
					$res['success'] = false;
					$res['msg'] = "Gagal mencari place.";
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
