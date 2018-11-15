<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\FAQ;
use Config;
use JWTAuth;

class FaqController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list()
	{
		$check = $this->authCheck();
		$code = 200;
		if ($check['success'] == true) {
			$data 	= FAQ::orderBy('id','desc')->get();
			if ($data->count() > 0) {
				$res['success'] = true;
				$res['faq'] = $data;
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Gagal mengambil FAQ.";
			}
		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res, $code);
	}
}