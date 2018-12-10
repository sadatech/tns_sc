<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\PropertiDc;
use JWTAuth;
use Config;
use DB;

class PropertyDcController extends Controller
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
			$propertyDc = PropertiDc::get();
			if ($propertyDc->count() > 0) {
				$listPropertyDc = [];
				$res['success'] = true;
				foreach ($propertyDc as $data) {
					$listPropertyDc[] = array(
						'id' 				=> $data->id,
						'item' 				=> $data->item,
					);
				}
				$res['property_dc'] = $listPropertyDc;
			} else {
				$res['success'] = false;
				$res['msg'] = "Kamu tidak mempunyai Properti DC.";
			}
			
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}