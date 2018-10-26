<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\DataPrice;
use App\DetailDataPrice;
use JWTAuth;
use Config;
use Carbon\Carbon;

class DataPriceController extends Controller
{
    public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		$data = json_decode($request->getContent());
		try {
			$res['code']=200;
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg']	= "User not found.";
				$res['code']= $e->getStatusCode();
			} else {
				if(!isset($data->store) || $data->store==null){
					$res['success'] = false;
					$res['msg'] = "Please select store.";
					unset($res['code']);
					return response()->json($res);
				}elseif(!isset($data->product) || empty($data->product)){
					$res['success'] = false;
					$res['msg'] = "Please select some product.";
					unset($res['code']);
					return response()->json($res);
				}else{
					$nullPrice = 0;
					$res = DB::transaction(function () use($request, $data, $res, $user, $nullPrice) {
						$date = Carbon::parse($data->date);
						$modelDataPrice = new DataPrice;
						$modelDataPrice->id_store = $data->store;
						$modelDataPrice->id_employee = $user->id;
						$modelDataPrice->date = $date;
						$modelDataPrice->save();
						foreach ($data->product as $product) {

							if($nullPrice == 1){
								break;
							}

							if(!isset($product->price) || $product->price == null){
								// unset($res['code']);
								$nullPrice = 1;
							}else{
								$modelDetailDataPrice = new DetailDataPrice;
								$modelDetailDataPrice->id_data_price = $modelDataPrice->id;
								$modelDetailDataPrice->id_product = $product->id;
								$modelDetailDataPrice->price = $product->price;
								$modelDetailDataPrice->save();
							}
						}

						if($nullPrice==0){
							$res['success'] = true;
							$res['msg'] = "Berhasil menambah data price.";
						}else{
							$res['success'] = false;
							$res['msg'] = "Price can't be null.";
							DB::rollback();
						}

						return $res;
					});
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
}
