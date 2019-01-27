<?php

namespace App\Http\Controllers\API;

use App\Components\traits\ApiAuthHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Components\traits\WeekHelper;
use App\Oos;
use App\OosDetail;
use JWTAuth;
use Config;
use Carbon\Carbon;

class OosController extends Controller
{
	use WeekHelper, ApiAuthHelper;
	
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
			$res = $check;

			$data = json_decode($request->getContent());

			if(!isset($data->store) || $data->store==null){
				$res['success'] = false;
				$res['msg'] = "Please select store.";
			}elseif(!isset($data->product) || empty($data->product)){
				$res['success'] = false;
				$res['msg'] = "Please select product.";
			}else{
				$insert = DB::transaction(function () use($request, $data, $res, $user) {
					$date 	= Carbon::parse($data->date);
					$oos = Oos::firstOrCreate([
						'id_employee'			=> $user->id,
						'id_store'				=> $data->store,
						'date'					=> Carbon::parse($date)->toDateString(),
						'week'					=> $this->getWeek($date),
					]);
					foreach ($data->product as $product) {
						OosDetail::updateOrCreate([
							'id_oos'	=> $oos->id,
							'id_product'=> $product->id,
						],[
							'qty'		=> $product->qty,
						]);
					}
					return "success";
				});

				if ($insert == "success") {
					$res['success'] = true;
					$res['msg'] 	= "Success create OOS.";
				} else {
					$res['success'] = false;
					$res['msg'] 	= "Fail create OOS.";
				}
				
			}

		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}
