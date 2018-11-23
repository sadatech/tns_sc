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
use App\MtcReportTemplate;
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
					$tempDate = Carbon::parse($data->date);
					$date 	= Carbon::create($tempDate->year, $tempDate->month, $tempDate->daysInMonth);
					$res 	= $this->sales($date, $user, $data->store, $data->product, $data->type);
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

	public function sales($date, $user, $request_store, $request_product, $type)
	{
		$checkSales = Sales::whereDate('date', $date)->where('type', $type)->first();
		$store = Store::where([
			'id' => $request_store,
		])->first();
		$res['code'] = 200;
		if (!$checkSales) {
			$sales = Sales::create([
				'id_employee'	=> $user->id,
				'id_store'		=> $request_store,
				'date'			=> $date,
				'week'			=> $date->weekOfMonth,
				'type'			=> $type,
			]);
			$sales_id = $sales->id;
		} else {
			$sales_id = $checkSales->id;
		}
		foreach ($request_product as $product) {
			$checkSalesDetail = DetailSales::where([
				'id_sales'		=> $sales_id,
				'id_product'	=> $product->id,
				'satuan'		=> $product->satuan,
			])->first();
			if (!$checkSalesDetail) {
				DetailSales::create([
					'id_sales'		=> $sales_id,
					'id_product'	=> $product->id,
					'qty'			=> $product->qty,
					'qty_actual'	=> $product->qty_actual,
					'satuan'		=> $product->satuan,
				]);
			}else{
				$checkSalesDetail->qty 			+= $product->qty;
				$checkSalesDetail->qty_actual 	+= $product->qty_actual;
				$checkSalesDetail->save();
			}
			
			$reportTemplate = MtcReportTemplate::where([
				'id_employee' 	=> $user->id,
				'id_store' 		=> $request_store,
				'id_product' 	=> $product->id
			])
			->whereYear('date',$date->year)
			->whereMonth('date',$date->month)
			->get();
			if ($reportTemplate->count() <= 0) {
				MtcReportTemplate::create([
					'id_employee' 	=> $user->id,
					'id_store' 		=> $request_store,
					'id_product' 	=> $product->id,
					'date' 			=> $date
				]);
			}

		}

		$res['success'] = true;
		$res['msg'] 	= "Berhasil melakukan sales.";
		return $res;
	}
}

