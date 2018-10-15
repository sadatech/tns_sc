<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use JWTAuth;
use Config;

class CategoryController extends Controller
{
	public function list()
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
			} else {
				$category = Category::get();
				if (!empty($category)) {
					$res['success'] = true;
					$dataArr = array();
					foreach ($category as $cat) {
						if ($cat->brand == null) {
							$brand = "Without Brand";
						} else {
							$brand = $cat->brand->name;
						}
						$dataArr[] = array(
							'id' => $cat->id,
							'brand' => $brand,
							'name' => $cat->name,
						);
					}
					$res['category'] = $dataArr;
				} else {
					$res['success'] = false;
					$res['msg'] = "Gagal mengambil category.";
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
