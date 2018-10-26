<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EmployeeStore;
use JWTAuth;
use Config;

class StoreController extends Controller
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
					$store = EmployeeStore::where([
						'id_employee' => $user->id
					])->with('store.account','store.distributor','store.subarea')->get(['id_store']);
					if (!$store->isEmpty()) {
						$storeArr = array();
						foreach ($store as $key => $value) {
							$storeArr[$key] = array(
								'id' => $value->store->id,
								'photo' => $value->store->photo,
								'name1' => $value->store->name1,
								'name2' => $value->store->name2,
								'address' => $value->store->address,
								'latitude' => $value->store->latitude,
								'longitude' => $value->store->longitude,
								'account' => $value->store->account->name,
								'channel' => $value->store->account->channel->name,
							);
						}
						$res['success'] = true;
						$res['store'] = $storeArr;
					} else {
						$res['msg'] = "Store not found.";
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
