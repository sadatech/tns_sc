<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\EmployeeStore;
use JWTAuth;
use Config;

class StoreController extends Controller
{
	public function list()
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['success'] = false;
				$res['msg'] = "User not found.";
				$code = 200;
			} else {
				$store = EmployeeStore::where([
					'id_employee' => $user->id
				])->with('store.province','store.city','store.account','store.distributor','store.classification','store.subarea')->get(['id_store','alokasi']);
				if (!$store->isEmpty()) {
					$storeArr = array();
					foreach ($store as $key => $value) {
						$storeArr[$key] = array(
							'id' => $value->store->id,
							'photo' => $value->store->photo,
							'name1' => $value->store->name1,
							'name2' => $value->store->name2,
							'store_phone' => $value->store->store_phone,
							'owner_phone' => $value->store->owner_phone,
							'address' => $value->store->address,
							'latitude' => $value->store->latitude,
							'longitude' => $value->store->longitude,
							'account' => $value->store->account->name,
							'channel' => $value->store->account->channel->name,
							'alokasi' => $value->alokasi
						);
					}
					$res['success'] = true;
					$res['store'] = $storeArr;
					$code = 200;
				} else {
					$res['success'] = false;
					$res['msg'] = "Gagal mencari store.";
					$code = 200;
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
