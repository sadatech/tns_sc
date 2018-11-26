<?php

// sisah ngambil is_target

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SalesMd;
use App\SalesMdDetail;
use App\Outlet;
use App\ProductFokus;
use App\Target;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;

class SalesMdController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$data = json_decode($request->getContent());

		if (empty($data->outlet) || empty($data->product) || empty($data->type) ) {
			$res['msg']	= "Please select Outlet and Product.";
			$res['code']= 200;
		}else
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg']	= "User not found.";
				$res['code']= 200;
			} else {
				DB::transaction(function () use ($data, $user, &$res) {
					$res 	= $this->sales($user, $data);
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
				$list = SalesMd::where([
					'id_employee' => $user->id,
				])->orderBy('created_at', 'desc')->get();
				if ($list) {
					$history = array();
					$productList = array();
					foreach ($list as $data) {
						$product = SalesMdDetail::where('id_sales', $data->id)->get();
						foreach ($product as $value) {
							$productList[] = array(
								'id' 		=> $value->id,
								'product' 	=> $value->product->name,
								'qty' 		=> $value->qty,
								'target' 	=> $value->target,
							);
						}
						$history = array(
							'id' 		=> $data->id,
							'outlet' 	=> $data->outlet->name,
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

	public function sales($user, $data)
	{

		$date 	= Carbon::parse($data->date);

		$checkSales = SalesMd::where([
			'id_employee'	=> $user->id,
			'id_outlet'		=> $data->outlet,
			'date'			=> $date,
			'type'			=> $data->type,
		])->first();

		$outlet = Outlet::where([
			'id' => $data->outlet,
		])->first();

		if ($outlet) {
			$res['code'] = 200;
			if (!$checkSales) {
				$sales = SalesMd::create([
					'id_employee'	=> $user->id,
					'id_outlet'		=> $data->outlet,
					'date'			=> $date,
					'week'			=> $date->weekOfMonth,
					'type'			=> $type,
				]);
				$sales_id = $sales->id;
			} else {
				$sales_id = $checkSales->id;
			}
			foreach ($data->product as $product) {
				$checkSalesDetail = SalesMdDetail::where([
					'id_sales'		=> $sales_id,
					'id_product'	=> $product->id,
					'satuan'		=> $product->satuan,
				])->first();
				if (!$checkSalesDetail) {
					
					$pf = ProductFokus::with('Fokus.channel')->
						whereHas('fokusproduct', function($query) use ($product)
						{
							return $query->where('id_product', $product->id);
						})
						->whereHas('Fokus.channel', function($query)
						{
							return $query->where('name','GTC');
						})->whereRaw("'$date' BETWEEN product_fokuses.from and product_fokuses.to")
						->get();

						$isPf = ($pf->count() > 0 ? 1 : 0);

					SalesMdDetail::create([
						'id_sales'		=> $sales_id,
						'id_product'	=> $product->id,
						'qty'			=> $product->qty,
						'qty_actual'	=> $product->qty_actual,
						'satuan'		=> $product->satuan,
						'is_pf'			=> $isPf,
					]);
				}else{
					$checkSalesDetail->qty 			+= $product->qty;
					$checkSalesDetail->qty_actual 	+= $product->qty_actual;
					$checkSalesDetail->save();
				}
			}

			$res['success'] = true;
			$res['msg'] 	= "Berhasil melakukan sales.";
		}else{
			$res['success'] = false;
			$res['msg'] 	= "Gagal melakukan sales. (Outlet not found)";
		}
		$res['code']= 200;
		return $res;
	}
}

