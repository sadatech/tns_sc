<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\SalesRecap;
use App\Outlet;
use DB;
use JWTAuth;
use Config;
use Carbon\Carbon;

class SalesRecapController extends Controller
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
			unset($check['user']);

			if (empty($request->outlet)) {
				$res['msg']		= "Please select Outlet.";
				$res['code']	= 200;
			}else
			DB::transaction(function () use ($request, $user, &$res) {
				$res['success'] = false;
				$res['msg'] 	= "Gagal melakukan sales.";
				$res['code'] 	= 200;

				$outlet = Outlet::where([
					'id' => $request->outlet,
				])->first();

				if ($outlet) {
					$date 	= Carbon::parse($request->date);
					if ($image 	= $request->file('photo')) {
						$photo 	= time()."_".$image->getClientOriginalName();
						$path 	= 'uploads/sales_recap';
						$image->move($path, $photo);
					}
					$sales = SalesRecap::updateOrCreate(
						[
							'id_employee'	=> $user->id,
							'id_outlet'		=> $request->outlet,
							'date'			=> $request->date,
						],
						[
							'total_buyer'	=> $request->total_buyer,
							'total_sales'	=> $request->total_sales,
							'total_value'	=> $request->total_value,
							'photo' 		=> $photo,
						]
					);

					if ($sales) {
						$res['success'] = true;
						$res['msg'] 	= "Berhasil melakukan sales.";
					}
				}else{
					$res['msg'] 	= "Outlet not found.";
				}
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
			unset($check['user']);

			$list = SalesRecap::where([
				'id_employee' => $user->id,
			])->orderBy('created_at', 'desc')->get();
			if ($list) {
				$history = array();
				foreach ($list as $data) {
					$history = array(
						'id' 			=> $data->id,
						'id_employee'	=> $data->id_employee,
						'id_outlet'		=> $data->id_outlet,
						'date'			=> $data->date,
						'total_buyer'	=> $data->total_buyer,
						'total_sales'	=> $data->total_buyer,
						'total_value'	=> $data->total_buyer,
						'photo' 		=> $data->photo,
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

}

