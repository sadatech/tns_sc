<?php

// sisah ngambil is_target

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\SalesSpgPasar;
use App\SalesSpgPasarDetail;
use App\Pasar;
use App\ProductFokus;
use App\Target;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;

class SalesSpgPasarController extends Controller
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
			if (empty($data->pasar) || empty($data->product) || empty($data->type) ) {
				$res['msg']	= "Please select Pasar and Product.";
				$res['code']= 200;
			}else
			DB::transaction(function () use ($data, $user, &$res) {
				$date 	= Carbon::parse($data->date);
				$res 	= $this->sales($date, $user, $data->pasar, $data->product, $data->type, $data->name, $data->phone);
			});
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function history()
	{
		$check = $this->authCheck();
		
		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

			$list = SalesSpgPasar::where([
				'id_employee' => $user->id,
			])->orderBy('created_at', 'desc')->get();
			if ($list) {
				$history = array();
				$productList = array();
				foreach ($list as $data) {
					$product = SalesSpgPasarDetail::where('id_sales', $data->id)->get();
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
						'pasar' 	=> $data->pasar->name,
						'date' 		=> $data->date,
						'name' 		=> $data->name,
						'phone' 	=> $data->phone,
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
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);	
	}

	public function sales($date, $user, $request_pasar, $request_product, $type, $name, $phone)
	{
		$pasar = Pasar::where([
			'id' => $request_pasar,
		])->first();

		if ($pasar) {
			$res['code'] = 200;
			
			$sales = SalesSpgPasar::firstOrCreate([
				'id_employee'	=> $user->id,
				'id_pasar'		=> $request_pasar,
				'date'			=> $date,
				'week'			=> $date->weekOfMonth,
				'type'			=> $type,
				'name' 			=> $name,
				'phone' 		=> $phone,
			]);
			$sales_id = $sales->id;
			
			foreach ($request_product as $product) {
				$checkSalesDetail = SalesSpgPasarDetail::where([
					'id_sales'		=> $sales_id,
					'id_product'	=> $product->id,
					'satuan'		=> $product->satuan,
				])->first();

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

				if (!$checkSalesDetail) {
					SalesSpgPasarDetail::create([
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
			$res['msg'] 	= "Gagal melakukan sales.";
		}
		$res['code']= 200;
		return $res;
	}
}

