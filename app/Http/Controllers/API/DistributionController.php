<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\Distribution;
use App\DistributionDetail;
use App\DistributionMotoric;
use App\DistributionMotoricDetail;
use App\Outlet;
use App\Block;
use JWTAuth;
use Config;
use DB;
use Carbon\Carbon;

class DistributionController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request, $type = 'MD')
	{
		
		$check = $this->authCheck();

		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

			$data = json_decode($request->getContent());

			if (strtoupper($type) == 'MOTORIC') {
				$status = ( Block::where('id', $data->block)->get()->count() > 0 ) ? true : false;
			}else{
				$status = ( Outlet::where('id', $data->outlet)->get()->count() > 0 ) ? true : false;
			}

			if ( !empty($data->date) && !empty($data->product) && ($status == true) ) {
				DB::transaction(function () use ($data, $user, &$res, $type) {
					$date 	= Carbon::parse($data->date);

					if (strtoupper($type) == 'MOTORIC') {
						$distribution = DistributionMotoric::firstOrCreate([
							'id_employee'	=> $user->id,
							'id_block'		=> $data->block,
							'date'			=> $date,
						]);
						$distributionDetailTemplate = new DistributionMotoricDetail;
					}else{
						$distribution = Distribution::firstOrCreate([
							'id_employee'	=> $user->id,
							'id_outlet'		=> $data->outlet,
							'date'			=> $date,
						]);
						$distributionDetailTemplate = new DistributionDetail;
					}

					$distributionId = $distribution->id;

					foreach ($data->product as $product) {

						$detail = $distributionDetailTemplate;

						$detail->updateOrCreate(
							[
								'id_distribution'		=> $distributionId,
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
					$res['msg'] 	= "Berhasil melakukan distribution.";

				});
			}else{
				$res['success'] = false;
				$res['msg'] 	= "Gagal melakukan distribution.";
			}

		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		
		return response()->json($res, $code);
	}

}
