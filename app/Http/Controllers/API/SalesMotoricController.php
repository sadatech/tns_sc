<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\SalesMotoric;
use App\SalesMotoricDetail;
use App\ProductFokus;
use App\Target;
use App\Employee;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;

class SalesMotoricController extends Controller
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

			$data = json_decode($request->getContent());
			DB::transaction(function () use ($data, $user, &$res) {
				$res 	= $this->sales($user, $data);
			});
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function sales($user, $data)
	{
		
		$date 	= Carbon::parse($data->date);
		$res['code'] = 200;
		$sales = new SalesMotoric;

		$sales = $sales->firstOrCreate([
			'id_employee'	=> $user->id,
			'block'			=> $data->block,
			'date'			=> $date,
			'week'			=> $date->weekOfMonth,
		]);

		$sales_id = $sales->id;

		foreach ($data->product as $product) {

			$salesDetail = new SalesMotoricDetail;

			$salesDetail->updateOrCreate(
				[
					'id_sales'		=> $sales_id,
					'id_product'	=> $product->id,
					'satuan'		=> $product->satuan,
				],
				[
					'qty'			=> \DB::raw("qty + ".$product->qty),
					'qty_actual'	=> \DB::raw("qty_actual + ".$product->qty_actual),
				]
			);
		}

		$res['success'] = true;
		$res['msg'] 	= "Berhasil melakukan sales.";

		$res['code']= 200;
		return $res;
	}
}

