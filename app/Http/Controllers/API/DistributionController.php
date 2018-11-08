<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Distribution;
use App\DistributionDetail;
use JWTAuth;
use Config;

class DistributionController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		return $request->getContent();
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					DB::transaction(function () use ($data, $user, &$res) {
						$date 	= Carbon::parse($data->date);
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
					});
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
