<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use JWTAuth;
use Config;

class CategoryController extends Controller
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
					$category = Category::get();
					if ($category->count() > 0) {
						$res['success'] = true;
						$dataArr = array();
						foreach ($category as $cat) {
							$dataArr[] = array(
								'id' => $cat->id,
								'name' => $cat->name,
							);
						}
						$res['category'] = $dataArr;
					} else {
						$res['msg'] = "Gagal mengambil kategori.";
					}
				}
			}else{
				$res['success'] = false;
				$res['msg'] = "User not found.";
				$code = 200;
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
