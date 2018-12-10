<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\Route;
use JWTAuth;
use Config;
use DB;

class RouteController extends Controller
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
			$route = Route::get();
			if ($route->count() > 0) {
				$listRoute = [];
				$res['success'] = true;
				foreach ($route as $data) {
					$listRoute[] = array(
						'id' 				=> $data->id,
						'name' 				=> $data->name,
						'id_subarea' 		=> $data->id_subarea,
						'subarea_name' 		=> $data->subarea->name,
					);
				}
				$res['route'] = $listRoute;
			} else {
				$res['success'] = false;
				$res['msg'] = "Kamu tidak mempunyai Root.";
			}
			
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}