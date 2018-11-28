<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\Area;
use JWTAuth;
use Config;

class AreaController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list()
	{
		
		$check = $this->authCheck();

		if ($check['success'] == true) {

			$user = $check['user'];
			unset($check['user']);
			$res = $check;

			$data = Area::get();
			if ($data->count() > 0) {
				$res['success'] = true;
				$dataArr = array();
				foreach ($data as $alias) {
					$dataArr[] = array(
						'id' 	=> $alias->id,
						'name' 	=> $alias->name,
					);
				}
				$res['area'] = $dataArr;
			} else {
				$res['msg'] = "Gagal mengambil area.";
			}
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res, $code);
	}
}
