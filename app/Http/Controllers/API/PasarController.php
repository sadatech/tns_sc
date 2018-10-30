<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pasar;
use App\EmployeePasar;
use JWTAuth;
use Config;
use DB;

class PasarController extends Controller
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
					$store = EmployeePasar::where([
						'id_employee' => $user->id
					])->with('pasar.subarea')->get(['id_pasar']);
					if (!$store->isEmpty()) {
						$storeArr = array();
						foreach ($store as $key => $value) {
							$storeArr[$key] = array(
								'id' => $value->pasar->id,
								'name' => $value->pasar->name,
								'address' => $value->pasar->address,
								'latitude' => (isset($value->pasar->latitude) ? $value->pasar->latitude : ""),
								'longitude' => (isset($value->pasar->longitude) ? $value->pasar->longitude : ""),
								'subarea' => $value->pasar->subarea->name
							);
						}
						$res['success'] = true;
						$res['pasar'] = $storeArr;
					} else {
						$res['msg'] = "Pasar not found.";
					}
				}
			}else{
				$res['msg'] = "User not found.";
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$code = $e->getStatusCode();

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$code = $e->getStatusCode();

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$code = $e->getStatusCode();
		}
		return response()->json($res, 200);
	}
}
