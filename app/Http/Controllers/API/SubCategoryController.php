<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SubCategory;
use JWTAuth;
use Config;

class SubCategoryController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list($id_category = '')
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					if ($id_category != 0) {
						$subCategory = SubCategory::where('id_category',$id_category)->get();
					}else{
						$subCategory = SubCategory::get();
					}
					if ($subCategory->count() > 0) {
						$res['success'] = true;
						$dataArr = array();
						foreach ($subCategory as $sub) {
							$dataArr[] = array(
								'id' 			=> $sub->id,
								'name' 			=> $sub->name,
								'description' 	=> $sub->description,
							);
						}
						$res['category'] = $dataArr;
					} else {
						$res['msg'] = "Gagal mengambil sub kategori.";
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
