<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Availability;
use App\DetailAvailability;
use JWTAuth;
use Config;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$data = json_decode($request->getContent());
		try {
			$res['code']=200;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg']	= "User not found.";
					$res['code']= $e->getStatusCode();
				} else {
					if(!isset($data->store) || $data->store==null){
						$res['success'] = false;
						$res['msg'] = "Please select store.";
						unset($res['code']);
						return response()->json($res);
					}elseif(!isset($data->product) || empty($data->product)){
						$res['success'] = false;
						$res['msg'] = "Please select some product.";
						unset($res['code']);
						return response()->json($res);
					}else{
						$res = DB::transaction(function () use($request, $data, $res, $user) {
							$date = Carbon::parse($data->date);
							$modelAvailability = new Availability;
							$modelAvailability->id_store = $data->store;
							$modelAvailability->id_employee = $user->id;
							$modelAvailability->date = $date;
							$modelAvailability->week = $data->week;
							$modelAvailability->save();
							foreach ($data->product as $product) {
								$modelDetailAvailability = new DetailAvailability;
								$modelDetailAvailability->id_availability = $modelAvailability->id;
								$modelDetailAvailability->id_product = $product->id;
								$modelDetailAvailability->available = $product->available;
								$modelDetailAvailability->save();
							}

							$res['success'] = true;
							$res['msg'] = "Berhasil set availability.";

							return $res;
						});
					}
				}
			}else{
				$res['msg'] = "User not found.";
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$res['code']= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$res['code']= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$res['code']= $e->getStatusCode();
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}
