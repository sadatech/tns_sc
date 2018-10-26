<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Promo;
use JWTAuth;
use Config;
use DB;

class PromoController extends Controller
{
	public function store(Request $request)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['success'] = false;
				$res['msg'] = "User not found.";
				$code = 200;
			} else {
				if ($image = $request->file('picture')) {
					$imageName = time()."_".$image->getClientOriginalName();
					$path = 'uploads/promo';
            		$image->move($path, $imageName);
				}
				$promo = Promo::create([
					'type' => $request->input('type'),
					'description' => $request->input('description'),
					'id_company' => $user->id_company,
					'from' => $request->input('from'),
					'to' => $request->input('to'),
					'picture' => (isset($imageName) ? $imageName : 'default.png')
				]);
				if ($promo) {
					$productList = array();
					foreach ($request->input('product') as $product) {
						$productList[] = array(
							'id_promo' => $promo->id,
							'id_product' => $product
						);
					}
					$insert = DB::table('promo_products')->insert($productList);
					if ($insert) {
						$res['success'] = true;
						$res['msg'] = "Berhasil menambah promo.";
						$code = 200;
					} else {
						$res['success'] = false;
						$res['msg'] = "Gagal menambah promo.";
						$code = 200;
					}
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