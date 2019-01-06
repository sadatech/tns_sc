<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\ProductFokusGtc;
use App\ProductCompetitor;
use App\FokusArea;
use App\FokusChannel;
use App\Pasar;
use App\Route;
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
					$product = Product::whereIdBrand($request->input('brand'))
					->when(!empty($request->input('subcategory')), function ($q) use ($request){
						return $q->whereIdSubcategory($request->input('subcategory'));
					})
					->when(!empty($request->input('category')), function ($q) use ($request){
						return $q->whereHas('subcategory', function($q2)
						{
							return $q->whereIdCategory($request->input('category'));
						});
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
					$area 	= (strtoupper($type) == 'PASAR') ? Pasar::where('id',$id)->first()->subarea->id_area : Route::where('id',$id)->first()->subarea->id_area;
					
					$pf 	= ProductFokusGtc::with(['product'])
					->whereRaw("'$today' BETWEEN product_fokus_gtcs.from and product_fokus_gtcs.to")
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

	public function listCompetitor()
	{
		$competitor = ProductCompetitor::get();

		return response()->json($competitor);
	}

	public function listCompetitorByCat($cat)
	{
		$competitor = ProductCompetitor::join('brands','product_competitors.id_brand','brands.id')
										->join('sub_categories','product_competitors.id_subcategory','sub_categories.id')
										->join('categories','sub_categories.id_category','categories.id')
										->where('categories.id',$cat)
										->select('product_competitors.*', 'categories.name as category_name', 'brands.name as brand_name')->get();

		return response()->json($competitor);
	}
	
	public function listCompetitorByCatBrand($cat, $brand)
	{
		$competitor = ProductCompetitor::where('id_brand',$brand)
										->join('brands','product_competitors.id_brand','brands.id')
										->join('sub_categories','product_competitors.id_subcategory','sub_categories.id')
										->join('categories','sub_categories.id_category','categories.id')
										->where('categories.id',$cat)
										->select('product_competitors.*', 'categories.name as category_name', 'brands.name as brand_name')->get();

		return response()->json($competitor);
	}

}