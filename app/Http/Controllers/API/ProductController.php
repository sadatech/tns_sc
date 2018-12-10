<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductFokusGtc;
use App\FokusArea;
use App\FokusChannel;
use App\Pasar;
use App\SubArea;
use Carbon\Carbon;
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
					$product = Product::whereIdBrand($request->input('brand'));
					$product->when(!empty($request->input('subcategory')), function ($q) use ($request){
						return $q->whereIdSubcategory($request->input('subcategory'));
					});
					if ($product->count() > 0) {
						$dataArr = array();
						foreach ($product->get() as $key => $pro) {
							$dataArr[] = array(
								'id' 			=> $pro->id,
								'name' 			=> $pro->name,
								'desctription' 	=> $pro->deskripsi,
								'panel' 		=> $pro->panel,
								'category' 		=> $pro->subcategory->category->name,
								'brand' 		=> $pro->brand->name,
								'carton' 		=> $pro->carton,
								'pack' 			=> $pro->pack,
								'pcs' 			=> $pro->pcs,
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

	public function pfList($type, $id)
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					$today 	= Carbon::today()->toDateString();
					$area 	= (strtoupper($type) == 'PASAR') ? Pasar::where('id',$id)->first()->subarea->id_area : SubArea::where('id',$id)->first()->id_area;
					
					$pf 	= ProductFokusGtc::with(['product'])
					->whereRaw("'$today' BETWEEN product_fokus_gtcs.from and to")
					->where( function($query) use ($area)
					{
						return $query->whereNull('id_area')->orWhere('id_area', $area);
					})
					->get();

					$product= [];
					foreach ($pf as $key => $value) {
						$product[] = $value->product;
					}
					if (sizeof($product) > 0) {
						$dataArr = array();
						foreach ($product as $key => $pro) {
							$dataArr[] = array(
								'id' 			=> $pro->id,
								'name' 			=> $pro->name,
								'desctription' 	=> $pro->deskripsi,
								'panel' 		=> $pro->panel,
								'category' 		=> $pro->subcategory->category->name,
								'brand' 		=> $pro->brand->name,
								'carton' 		=> $pro->carton,
								'pack' 			=> $pro->pack,
								'pcs' 			=> $pro->pcs,
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