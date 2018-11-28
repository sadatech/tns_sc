<?php

// sisah ngambil is_target

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\SalesDc;
use App\SalesDcDetail;
use App\SamplingDc;
use App\SamplingDcDetail;
use App\ProductFokus;
use App\Target;
use App\Employee;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;

class SalesDcController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request, $type = 'SALES')
	{

		$check = $this->authCheck();

		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$data = json_decode($request->getContent());
			DB::transaction(function () use ($data, $user, &$res, $type) {
				$res 	= $this->sales($user, $data, $type);
			});
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function sales($user, $data, $type = 'SALES')
	{
		
		$date 	= Carbon::parse($data->date);
		
		$res['code'] = 200;
		
		if (strtoupper($type) == 'SALES') {
			$sales = new SalesDc;
			$salesDetailTemplate = new SalesDcDetail;
		}else{
			$sales = new SamplingDc;
			$salesDetailTemplate = new SamplingDcDetail;
		}

		$sales = $sales->firstOrCreate([
			'id_employee'	=> $user->id,
			'place'			=> $data->place,
			'date'			=> $date,
			'week'			=> $date->weekOfMonth,
		]);

		$sales_id = $sales->id;
		
		foreach ($data->product as $product) {

			$salesDetail = $salesDetailTemplate;

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
		$res['msg'] 	= "Berhasil melakukan $type.";

		$res['code']= 200;
		return $res;
	}
}

