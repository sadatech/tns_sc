<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\News;
use Config;
use JWTAuth;

class NewsController extends Controller
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
			$data 	= News::get();
			if ($data->count() > 0) {
				$res['success'] = true;
				$res['news'] = $data;
			} else {
				$res['msg'] 	= "Gagal mengambil News.";
			}
		}else{
			$res = $check;
			$code = $res['code'];
			unset($res['code']);
		}
		
		return response()->json($res, $code);
	}
}