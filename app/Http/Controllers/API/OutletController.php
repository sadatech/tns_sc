<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use JWTAuth;
use Config;

class OutletController extends Controller
{
	public function store(Request $request)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$data = json_decode($request->getContent());
				if (empty($data->pasar) || empty($data->name) || empty($data->phone)) {
					$res['success'] = false;
					$res['msg'] = "Data cannot be empty.";
					$code = 200;
				} else {
					$emp = EmployeePasar::where([
						'id_pasar' => $request->input('pasar'),
						'id_employee' => $user->id
					])->first();
					if (!empty($emp)) {
						$outlets = array();
						foreach ($data->outlet as $data) {
							$outlets[] = array(
								'id_employee_pasar'	=> $emp->id,
								'name'				=> $data->name,
								'phone'				=> $data->phone,
							);
						}
						$insert = DB::table('outlets')->insert($outlets);
						if ($insert) {
							$res['success'] = true;
							$res['msg'] = "Success add outlets.";
							$code = 200;
						} else {
							$res['success'] = false;
							$res['msg'] = "Failed to add outlets.";
							$code = 200;
						}
					}
				}
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
		return response()->json($res, $code);
	}
}
