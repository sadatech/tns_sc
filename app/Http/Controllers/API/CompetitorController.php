<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Competitor;
use App\CompetitorPromo;
use JWTAuth;
use Config;

class CompetitorController extends Controller
{
	public function list()
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$competitor = Competitor::where('id_company', $user->id_company)->get(['id', 'name', 'deskripsi']);
				$res['success'] = true;
				$res['competitor'] = $competitor;
				$code = 200;
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$code = $e->getStatusCode();
		}
		return response()->json($res, $code);
	}

	public function promo(Request $request)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$data=$request->all();
				$limit=[
					'competitor'	=> 'required',
					'type'			=> 'required',
					'sku' 			=> 'required',
					'keterangan'	=> 'required',
					'from' 			=> 'required',
					'to' 			=> 'required',
				];
				$validator = Validator($data, $limit);
				if ($validator->fails()){
					$res['success'] = false;
					$res['msg'] = $validator->errors()->first();
					$code = 200;
				} else {
					if ($image = $request->file('picture')) {
						$imageName = time()."_".$image->getClientOriginalName();
						$path = 'uploads/promo';
						$image->move($path, $imageName);
					}
					$competitor = CompetitorPromo::create([
						'id_competitor' => $request->input('competitor'),
						'id_company' => $user->id_company,
						'type' => $request->input('type'),
						'sku' => $request->input('sku'),
						'description' => $request->input('keterangan'),
						'from' => $request->input('from'),
						'to' => $request->input('to'),
						'picture' => (isset($imageName) ? $imageName : 'default.png')
					]);
					if ($competitor) {
						$res['success'] = true;
						$res['msg'] = "Berhasil menambah competitor promo.";
						$code = 200;
					} else {
						$res['success'] = false;
						$res['msg'] = "Gagal menambah competitor promo.";
						$code = 200;
					}
				}
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$code = $e->getStatusCode();
		}
		return response()->json($res, $code);
	}

	public function history()
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['success'] = false;
				$res['msg'] = "User not found.";
				$code = 200;
			} else {
				$list = CompetitorPromo::where([
					'id_company' => $user->id_company,
					'id_employee' => $user->id,
				])->orderBy('created_at', 'desc')->get();
				if ($list) {
					$history = array();
					foreach ($list as $data) {
						$history[] = array(
							'id' => $data->id,
							'sku' => $data->sku,
							'type' => $data->type,
							'competitor' => $data->competitor->name,
							'description' => $data->description,
							'from' => $data->from,
							'to' => $data->to
						);
					}
					$res['success'] = true;
					$res['msg'] = "Berhasil mengambil data.";
					$res['list'] = $history;
					$code = 200;
				} else {
					$res['success'] = false;
					$res['msg'] = "Tidak ada data";
					$code = 200;
				}
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
			$code = $e->getStatusCode();
		}
		return response()->json($res, $code);	
	}
}
