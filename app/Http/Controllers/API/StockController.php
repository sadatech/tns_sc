<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;
use App\StockMdHeader as MDHeader;
use App\StockMdDetail as MDDetail;
use App\SkuUnit;

class StockController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$code = 200;
				try {
					$data = json_decode($request->getContent());
					DB::beginTransaction();
					$date = Carbon::parse($data->date);
					$checkStock = MDHeader::where([
						'id_employee' 	=> $user->id,
						'id_pasar' 		=> $data->pasar,
						'stockist' 		=> $data->stockist,
						'date' 			=> $date,
					])->first();

					if (!$checkStock) {
						
						$header = MDHeader::create([
							'id_employee' 	=> $user->id,
							'id_pasar' 		=> $data->pasar,
							'stockist' 		=> $data->stockist,
							'date' 			=> $date,
							'week' 			=> $date->weekOfMonth,
						]);
						$headerId = $header->id;
					}else{
						$headerId = $checkStock->id;
					}
					if (isset($headerId)) {
						foreach ($data->product as $product) {
							$detail = MDDetail::create([
								'id_stock' 		=> $headerId,
								'id_product' 	=> $product->id,
								'oos' 			=> $product->oos,
							]);
							if (!isset($detail->id)) {
								throw new Exception("Error Processing Request", 1);
							}
						}
						DB::commit();
						$res['success'] = true;
						$res['msg'] = "Berhasil menambah stock.";
					} else {
						DB::rollback();
						$res['success'] = false;
						$res['msg'] = "Gagal menambah stock.";
					}
				} catch (Exception $e) {
					DB::rollback();
					$res['success'] = false;
					$res['msg'] = "Gagal menambah stock.";
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
