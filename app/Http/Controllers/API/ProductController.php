<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductMeasure;
use App\MeasurementUnit;
use Config;
use JWTAuth;

class ProductController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list(Request $request)
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					if (!empty($request->input('subcategory'))) {
						$where = [
							'id_brand' 			=> $request->input('brand'),
							'id_subcategory' 	=> $request->input('subcategory')
						];
					}else{
						$where = [
							'id_brand' 			=> $request->input('brand')
						];
					}
					$product 	= Product::where($where)->get();
					if ($product->count() > 0) {
						$dataArr = array();
						foreach ($product as $key => $pro) {
							$productMeasure = ProductMeasure::where('id_product',$pro->id)->get();
							$measure = [];
							foreach ($productMeasure as $key2 => $value2) {
								$measure[] = array(
									'id' => $value2->measure->id,
									'name' => $value2->measure->name, 
									'size' => $value2->measure->size 
								);
							}
							$dataArr[] = array(
								'id' 			=> $pro->id,
								'name' 			=> $pro->name,
								'desctription' 	=> $pro->deskripsi,
								'panel' 		=> $pro->panel,
								'subcategory' 	=> $pro->subcategory->name,
								'brand' 		=> $pro->brand->name,
								'measure' 		=> $measure,
							);
						}
						$res['success'] = true;
						$res['product'] = $dataArr;
					} else {
						$res['msg'] 	= "Gagal mengambil produk.";
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