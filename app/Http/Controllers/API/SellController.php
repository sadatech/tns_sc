<?php

// sisah ngambil is_target

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SellIn;
use App\SellOut;
use App\Stock;
use App\DetailIn;
use App\DetailOut;
use App\StockDetail;
use App\Store;
use App\ProductFokus;
use App\Target;
use App\Price;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;

class SellController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request, $type)
	{
		$data = json_decode($request->getContent());
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg']	= "User not found.";
				$res['code']= $e->getStatusCode();
			} else {
				$date = Carbon::parse($data->date);
				$date2 = Carbon::parse($data->date);
				if ($type == 1) {
					$res = $this->sellin($date, $date2, $user, $data->store, $data->product);
				} else if ($type == 2) {
					$res = $this->sellout($date, $date2, $user, $data->store, $data->product);
				} else if ($type == 3) {
					$res = $this->stock($date, $date2, $user, $data->store, $data->product);
				}
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

	public function history($type = 1)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['success'] = false;
				$res['msg'] = "User not found.";
				$code = 200;
			} else {
				if ($type == 1) {
					$list = SellIn::where([
						'id_employee' => $user->id,
					])->orderBy('created_at', 'desc')->get();
					if ($list) {
						$history = array();
						$productList = array();
						foreach ($list as $data) {
							$product = DetailIn::where('id_sellin', $data->id)->get();
							foreach ($product as $value) {
								$productList[] = array(
									'id' => $value->id,
									'product' => $value->product->name,
									'price' => $value->price,
									'qty' => $value->qty,
									'target' => $value->target,
								);
							}
							$history = array(
								'id' => $data->id,
								'store' => $data->store->name1,
								'date' => $data->date,
								'week' => $data->week,
								'products' => $productList
							);
						}
						$res['success'] = true;
						$res['msg'] = "Berhasil mengambil data.";
						$res['list'] = $history;
					} else {
						$res['success'] = false;
						$res['msg'] = "Data tidak ditemukan.";
						$res['list'] = $history;
					}
				} else if ($type == 2) {
					$list = SellOut::where([
						'id_employee' => $user->id,
					])->orderBy('created_at', 'desc')->get();
					if ($list) {
						$history = array();
						$productList = array();
						foreach ($list as $data) {
							$product = DetailOut::where('id_sellout', $data->id)->get();
							foreach ($product as $value) {
								$productList[] = array(
									'id' => $value->id,
									'product' => $value->product->name,
									'price' => $value->price,
									'qty' => $value->qty,
									'target' => $value->target,
								);
							}
							$history = array(
								'id' => $data->id,
								'store' => $data->store->name1,
								'date' => $data->date,
								'week' => $data->week,
								'products' => $productList
							);
						}
						$res['success'] = true;
						$res['msg'] = "Berhasil mengambil data.";
						$res['list'] = $history;
					} else {
						$res['success'] = false;
						$res['msg'] = "Data tidak ditemukan.";
						$res['list'] = $history;
					}
				} else if ($type == 3) {
					$list = Stock::where([
						'id_employee' => $user->id,
					])->orderBy('created_at', 'desc')->get();
					if ($list) {
						$history = array();
						$productList = array();
						foreach ($list as $data) {
							$product = StockDetail::where('id_stock', $data->id)->get();
							foreach ($product as $value) {
								$productList[] = array(
									'id' => $value->id,
									'product' => $value->product->name,
									'price' => $value->price,
									'qty' => $value->qty,
								);
							}
							$history = array(
								'id' => $data->id,
								'store' => $data->store->name1,
								'date' => $data->date,
								'week' => $data->week,
								'products' => $productList
							);
						}
						$res['success'] = true;
						$res['msg'] = "Berhasil mengambil data.";
						$res['list'] = $history;
					} else {
						$res['success'] = false;
						$res['msg'] = "Data tidak ditemukan.";
						$res['list'] = $history;
					}
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

	public function sellin($date, $date2, $user, $request_store, $request_product)
	{
		$checkSellIn = SellIn::where('week', $date->weekOfMonth)->first();
		$store = Store::where([
			'id' => $request_store,
		])->first();
		$res['code'] = 200;
		if (!$checkSellIn) {
			$sellin = SellIn::create([
				'id_employee'	=> $user->id,
				'id_store'		=> $request_store,
				'date'			=> $date2,
				'week'			=> $date->weekOfMonth,
			]);
			if ($sellin) {
				$detailin = array();
				foreach ($request_product as $product) {
					$detailin[] = array(
						'id_sellin'		=> $sellin->id,
						'id_product'	=> $product->id,
						'qty'			=> $product->qty,
					);
				}
				$insert_in = DB::table('detail_ins')->insert($detailin);
				if ($insert_in) {
					$res['success'] = true;
					$res['msg'] = "Berhasil melakukan sell in.";
				} else {
					$res['success'] = false;
					$res['msg'] = "Gagal melakukan sell in.";
				}
			}
		} else {
			$detailin = array();
			foreach ($request_product as $product) {
				$detailin[] = array(
					'id_sellin'		=> $checkSellIn->id,
					'id_product'	=> $product->id,
					'qty'			=> $product->qty,
				);
			}
			$insert_in = DB::table('detail_ins')->insert($detailin);
			if ($insert_in) {
				$res['success'] = true;
				$res['msg'] = "Berhasil melakukan sell in.";
			} else {
				$res['success'] = false;
				$res['msg'] = "Gagal melakukan sell in.";
			}
		}
		return $res;
	}

	public function sellout($date, $date2, $user, $request_store, $request_product)
	{
		$checkSellOut = SellOut::where('week', $date->weekOfMonth)->first();
		$store = Store::where([
			'id' => $request_store,
		])->first();
		if (!$checkSellOut) {
			$sellout = SellOut::create([
				'id_employee'	=> $user->id,
				'id_store'		=> $request_store,
				'date'			=> $date2,
				'week'			=> $date->weekOfMonth,
			]);
			if ($sellout) {
				$detailout = array();
				foreach ($request_product as $product) {
					$detailout[] = array(
						'id_sellout'	=> $sellout->id,
						'id_product'	=> $product->id,
						'qty'			=> $product->qty,
					);
				}
				$insert_in = DB::table('detail_out')->insert($detailout);
				if ($insert_in) {
					$res['success'] = true;
					$res['msg'] = "Berhasil melakukan sell out.";
					$res['code']= 200;
				} else {
					$res['success'] = false;
					$res['msg'] = "Gagal melakukan sell out.";
					$res['code']= 200;
				}
			}
		} else {
			$detailout = array();
			foreach ($request_product as $product) {
				$detailout[] = array(
					'id_sellout'	=> $checkSellOut->id,
					'id_product'	=> $product->id,
					'qty'			=> $product->qty,
				);
			}
			$insert_out = DB::table('detail_outs')->insert($detailout);
			if ($insert_out) {
				$res['success'] = true;
				$res['msg'] = "Berhasil melakukan sell out.";
				$res['code']= 200;
			} else {
				$res['success'] = false;
				$res['msg'] = "Gagal melakukan sell out.";
				$res['code']= 200;
			}
		}
		return $res;
	}

	public function stock($date, $date2, $user, $request_store, $request_product)
	{
		$checkStock = Stock::where('week', $date->weekOfMonth)->first();
		$store = Store::where([
			'id' => $request_store,
		])->first();
		if (!$checkStock) {
			$stock = Stock::create([
				'id_employee'	=> $user->id,
				'id_store'		=> $request_store,
				'date'			=> $date2,
				'week'			=> $date->weekOfMonth,
			]);
			if ($stock) {
				$detailstock = array();
				foreach ($request_product as $product) {
					$detailstock[] = array(
						'id_stock'	=> $stock->id,
						'id_product'	=> $product->id,
						'qty'			=> $product->qty,
					);
				}
				$insert_stock = DB::table('stock_details')->insert($detailstock);
				if ($insert_stock) {
					$res['success'] = true;
					$res['msg'] = "Berhasil menambah stock.";
					$res['code']= 200;
				} else {
					$res['success'] = false;
					$res['msg'] = "Gagal menambah stock.";
					$res['code']= 200;
				}
			}
		} else {
			$detailstock = array();
			foreach ($request_product as $product) {
				$detailstock[] = array(
					'id_stock'	=> $checkStock->id,
					'id_product'	=> $product->id,
					'qty'			=> $product->qty,
				);
			}
			$insert_stock = DB::table('stock_details')->insert($detailstock);
			if ($insert_stock) {
				$res['success'] = true;
				$res['msg'] = "Berhasil menambah stock.";
				$res['code']= 200;
			} else {
				$res['success'] = false;
				$res['msg'] = "Gagal menambah stock.";
				$res['code']= 200;
			}
		}
		return $res;
	}
}

