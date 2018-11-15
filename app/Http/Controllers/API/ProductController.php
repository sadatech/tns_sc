<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductFokus;
use App\FokusArea;
use App\FokusChannel;
use App\Pasar;
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
		return response()->json(Product::get()->toArray());
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

	public function pfList($id_pasar)
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					$today 	= Carbon::today()->toDateString();
					$area 	= Pasar::find($id_pasar)->first()->subarea->id_area;
					$pf 	= ProductFokus::with('Fokus.channel')->
					whereHas('Fokus', function($query)
					{
						return $query->whereHas('channel', function($query2)
						{
							return $query2->where('name','GTC');
						});
					})->whereRaw("'$today' BETWEEN product_fokuses.from and product_fokuses.to")->get();

					$product= [];
					foreach ($pf as $key => $value) {
						$areas__ = FokusArea::where('id_pf',$value->id)->get();
						if ($areas__->count() == 0) {
							$product[] = $value->product;
						}else{
							foreach ($areas__ as $key2 => $value2) {
								if ($value2->id_area == $area) {
									$product[] = $value->product;
								}
							}
						}
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