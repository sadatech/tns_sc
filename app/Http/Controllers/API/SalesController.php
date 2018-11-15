<?php

// sisah ngambil is_target

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sales;
use App\DetailSales;
use App\Store;
use App\ProductFokus;
use App\Target;
use App\Price;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;

class SalesController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$data = json_decode($request->getContent());

		if (empty($data->store) || empty($data->product) || empty($data->type) ) {
			$res['msg']	= "Please select Store and Product.";
			$res['code']= 200;
		}else
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg']	= "User not found.";
				$res['code']= 200;
			} else {
				DB::transaction(function () use ($data, $user, &$res) {
					$date 	= Carbon::parse($data->date);
					$date2 	= Carbon::parse($data->date);
					$res 	= $this->sales($date, $date2, $user, $data->store, $data->product, $data->type);
				});
			}
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
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['success'] = false;
				$res['msg'] 	= "User not found.";
				$code 			= 200;
			} else {
				$list = Sales::where([
					'id_employee' => $user->id,
				])->orderBy('created_at', 'desc')->get();
				if ($list) {
					$history = array();
					$productList = array();
					foreach ($list as $data) {
						$product = DetailSales::where('id_sales', $data->id)->get();
						foreach ($product as $value) {
							$productList[] = array(
								'id' 		=> $value->id,
								'product' 	=> $value->product->name,
								'price' 	=> $value->price,
								'qty' 		=> $value->qty,
								'target' 	=> $value->target,
							);
						}
						$history = array(
							'id' 		=> $data->id,
							'store' 	=> $data->store->name1,
							'date' 		=> $data->date,
							'week' 		=> $data->week,
							'products' 	=> $productList
						);
					}
					$res['success'] = true;
					$res['msg'] 	= "Berhasil mengambil data.";
					$res['list'] 	= $history;
				} else {
					$res['success'] = false;
					$res['msg'] 	= "Data tidak ditemukan.";
					$res['list'] 	= $history;
				}
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$code 		= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$code 		= $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$code 		= $e->getStatusCode();
		}
		return response()->json($res, $code);	
	}

	public function sales($date, $date2, $user, $request_store, $request_product, $type)
	{
		$checkSales = Sales::where('week', $date->weekOfMonth)->where('type', $type)->first();
		$store = Store::where([
			'id' => $request_store,
		])->first();
		$res['code'] = 200;
		if (!$checkSales) {
			$sales = Sales::create([
				'id_employee'	=> $user->id,
				'id_store'		=> $request_store,
				'date'			=> $date2,
				'week'			=> $date->weekOfMonth,
				'type'			=> $type,
			]);
			if ($sales) {
				$detailSales = array();
				foreach ($request_product as $product) {
					$detailSales[] = array(
						'id_sales'		=> $sales->id,
						'id_product'	=> $product->id,
						'qty'			=> $product->qty,
						'qty_actual'	=> $product->qty_actual,
						'satuan'	=> $product->satuan,
					);
				}
				$insert_sales = DB::table('detail_sales')->insert($detailSales);
				if ($insert_sales) {
					$res['success'] = true;
					$res['msg'] 	= "Berhasil melakukan sales.";
				} else {
					$res['success'] = false;
					$res['msg'] 	= "Gagal melakukan sales.";
				}
			}
		} else {
			$detailSales = array();
			foreach ($request_product as $product) {
				$detailSales[] = array(
					'id_sales'		=> $checkSales->id,
					'id_product'	=> $product->id,
					'qty'			=> $product->qty,
					'qty_actual'	=> $product->qty_actual,
					'satuan'	=> $product->satuan,
				);
			}
			$insert_sales = DB::table('detail_sales')->insert($detailSales);
			if ($insert_sales) {
				$res['success'] = true;
				$res['msg'] 	= "Berhasil melakukan sales.";
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Gagal melakukan sales.";
			}
		}
		return $res;
	}
}

