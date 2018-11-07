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
	public function store(Request $request)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				try {
					DB::beginTransaction();
					$data = json_decode($request->getContent());
					$date = Carbon::parse($data->date);
					$header = MDHeader::create([
						'id_employee' => $user->id,
						'id_pasar' => $data->pasar,
						'stockist' => $data->stockist,
						'date' => $date,
						'week' => $date->weekOfMonth,
					]);
					if (isset($header->id)) {
						foreach ($data->product as $product) {
							$detail = MDDetail::create([
								'id_stock' => $header->id,
								'id_product' => $product->id,
								'id_satuan' => $product->satuan,
							]);
							if (!isset($detail->id)) {
								throw new Exception("Error Processing Request", 1);
							}
						}
						DB::commit();
						$res['success'] = true;
						$res['msg'] = "Berhasil menambah stock.";
						$code = 200;
					} else {
						DB::rollback();
						$res['success'] = false;
						$res['msg'] = "Gagal menambah stock.";
						$code = 200;
					}
				} catch (Exception $e) {
					DB::rollback();
					$res['success'] = false;
					$res['msg'] = "Gagal menambah stock.";
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
