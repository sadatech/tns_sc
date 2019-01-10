<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use JWTAuth;
use Config;
use Image;
use DB;
use App\PromoDetail;
use App\Promo;

class PromoController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$request = json_decode($request->getContent());
		
		$res['code'] = 200;
		if (empty($request->store) || empty($request->detail)) {
			$res['msg']	= "Please select Store and Product.";
		}else
		try {
			DB::transaction(function () use ($request, &$res) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['success'] = false;
					$res['msg'] 	= "User not found.";
				} else {
					
					$promo = Promo::create([
						'id_employee' 	=> $user->id,
						'id_store' 		=> $request->store,
					]);
					if ($promo) {
						$res['id'] = $promo->id;
						$productList = array();
						foreach ($request->detail as $product) {
							$productList[] = array(
								'id_promo' 				=> $promo->id,
								'id_product' 			=> $product->product,
								'id_product_competitor'	=> $product->product_competitor,
								'type' 					=> $product->type,
								'description' 			=> $product->description,
								'start_date' 			=> Carbon::parse($product->start_date),
								'end_date'				=> Carbon::parse($product->end_date),
								'created_at'    		=> Carbon::now(),
								'updated_at'    		=> Carbon::now()
							);
						}
						$insert = DB::table('promo_details')->insert($productList);
						if ($insert) {
							$res['success'] = true;
							$res['msg'] 	= "Berhasil menambah promo.";
						} else {
							$res['success'] = false;
							$res['msg'] 	= "Gagal menambah promo.";
						}
					}
				}
			});
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$res['code']= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$res['code']= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$res['code']= $e->getStatusCode();
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function store_image(Request $request)
	{
		$res['code'] = 200;
		if (empty($request->input('id'))) {
			$res['msg']	= "Promo ID cannot be empty.";
		}else
		try {
			DB::transaction(function () use ($request, &$res) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['success'] = false;
					$res['msg'] 	= "User not found.";
				} else {
					if ($image = $request->file('image1')) {
						$imageName 	= time()."_".$image->getClientOriginalName();
						$path 		= 'uploads/promo';
						$image->move($path, $imageName);
						$image_compress = Image::make($path.'/'.$imageName)->orientate();
						$image_compress->save($path.'/'.$imageName, 50);
					}
					if ($image = $request->file('image2')) {
						$imageName2 = time()."_".$image->getClientOriginalName();
						$path 		= 'uploads/promo';
						$image->move($path, $imageName2);
						$image_compress = Image::make($path.'/'.$imageName2)->orientate();
						$image_compress->save($path.'/'.$imageName2, 50);
					}
					if ($image = $request->file('image3')) {
						$imageName3 = time()."_".$image->getClientOriginalName();
						$path 		= 'uploads/promo';
						$image->move($path, $imageName3);
						$image_compress = Image::make($path.'/'.$imageName3)->orientate();
						$image_compress->save($path.'/'.$imageName3, 50);
					}
					$promo = Promo::whereId($request->input('id'))->update([
						'image1' 		=> (isset($imageName)?$imageName:''),
						'image2' 		=> (isset($imageName2)?$imageName2:''),
						'image3' 		=> (isset($imageName3)?$imageName3:''),
					]);

					if ($promo) {
						$res['success'] = true;
						$res['msg'] 	= "Update Success.";
					} else {
						$res['success'] = false;
						$res['msg'] 	= "Update Fail.";
					}
				}
			});
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$res['code']= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$res['code']= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$res['code']= $e->getStatusCode();
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function history()
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['success'] = false;
				$res['msg'] = "User not found.";
				$code = 200;
			} else {
				$list = Promo::where([
					'id_company' => $user->id_company,
					'id_employee' => $user->id,
				])->orderBy('created_at', 'desc')->get();
				if ($list) {
					$history = array();
					$productList = array();
					foreach ($list as $data) {
						$product = PromoProduct::where('id_promo', $data->id)->get();
						foreach ($product as $value) {
							$productList[] = array(
								'product' => $value->product->name,
							);
						}
						$history[] = array(
							'id' => $data->id,
							'type' => $data->type,
							'description' => $data->description,
							'from' => $data->from,
							'to' => $data->to,
							'picture' => $data->picture,
							'products' => $productList
						);
					}
					$res['success'] = true;
					$res['msg'] = "Berhasil mengambil data.";
					$res['list'] = $history;
					$code = 200;
				} else {
					$res['success'] = false;
					$res['msg'] = "Tidak ada data";
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