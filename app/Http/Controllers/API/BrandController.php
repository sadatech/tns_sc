<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Brand;
use App\Price;
use App\Store;
use Config;
use JWTAuth;

class BrandController extends Controller
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
					$brands = Brand::get();
					if ($brands->count() > 0) {
						$dataArr = array();
						foreach ($brands as $key => $brand) {
							$dataArr[] = array(
								'id' 		=> $brand->id,
								'name' 		=> $brand->name,
								'keterangan'=> $brand->keterangan,
							);
						}
						$res['success'] = true;
						$res['brand'] 	= $dataArr;
					} else {
						$res['msg'] 	= "Gagal mengambil brand.";
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