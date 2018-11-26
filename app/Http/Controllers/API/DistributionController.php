<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Distribution;
use App\DistributionDetail;
use App\Outlet;
use JWTAuth;
use Config;
use DB;
use Carbon\Carbon;

class DistributionController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$data = json_decode($request->getContent());
		try {
			$res['success'] = false;
			$res['code'] = 200;
			
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					$outlet = Outlet::where('id', $data->outlet)->get();
					if ( !empty($data->date) && !empty($data->outlet) && !empty($data->product) && ($outlet->count() > 0) ) {
						DB::transaction(function () use ($data, $user, &$res) {
							$date 	= Carbon::parse($data->date);
							$checkDistribution = Distribution::where([
								'id_employee'	=> $user->id,
								'id_outlet'		=> $data->outlet,
								'date'			=> $date,
							])->first();
							if (!$checkDistribution) {
								$distribution = Distribution::create([
									'id_employee'	=> $user->id,
									'id_outlet'		=> $data->outlet,
									'date'			=> $date,
								]);
								$distributionId = $distribution->id;
							} else {
								$distributionId	= $checkDistribution->id;
							}
							if (isset($distributionId)) {
								$distributionDetail = array();
								foreach ($data->product as $product) {
									$detail = DistributionDetail::where([
										'id_distribution'	=> $distributionId,
										'id_product'		=> $product->id,
									])->first();
									if (!$detail) {
										DistributionDetail::create([
											'id_distribution'	=> $distributionId,
											'id_product'		=> $product->id,
											'qty'				=> $product->qty,
											'qty_actual'		=> $product->qty_actual,
											'satuan'			=> $product->satuan,
										]);
									}else{
										$detail->qty = $product->qty;
										$detail->qty_actual = $product->qty_actual;
										$detail->save();
									}
								}
								$res['success'] = true;
								$res['msg'] 	= "Berhasil melakukan distribution.";
							}
						});
					}else{
						$res['success'] = false;
						$res['msg'] 	= "Gagal melakukan distribution.";
					}
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

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}
