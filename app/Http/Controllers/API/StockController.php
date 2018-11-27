<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;
use App\StockMdHeader as MDHeader;
use App\StockMdDetail as MDDetail;
use App\SkuUnit;
use Exception;

class StockController extends Controller
{

	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$check = $this->authCheck();

		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

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
						$checkDetail = MDDetail::where([
							'id_stock' 		=> $headerId,
							'id_product' 	=> $product->id,
						])->first();
						if (!$checkDetail) {
							$detail = MDDetail::create([
								'id_stock' 		=> $headerId,
								'id_product' 	=> $product->id,
								'oos' 			=> $product->oos,
							]);
							if (!isset($detail->id)) {
								throw new Exception("Error Processing Request", 1);

							}
						}else{
							$checkDetail->oos = $product->oos;
							$checkDetail->save();
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

		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res, $code);
	}
}
