<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\CashAdvance;
use Carbon\Carbon;
use JWTAuth;
use Config;
use DB;
class CashAdvanceController extends Controller
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
			$res = $check;
			if (empty($request->area) || empty($request->date) ) {
				$res['success'] = false;
				$res['msg'] 	= "Area and Date cannot be empty.";
			} else {
					$total_cost_list[] = (!empty($request->tpd) ? $request->tpd : 0);
					$total_cost_list[] = (!empty($request->hotel) ? $request->hotel : 0);
					$total_cost_list[] = (!empty($request->bbm) ? $request->bbm : 0);
					$total_cost_list[] = (!empty($request->parking_and_toll) ? $request->parking_and_toll : 0);
					$total_cost_list[] = (!empty($request->raw_material) ? $request->raw_material : 0);
					$total_cost_list[] = (!empty($request->property) ? $request->property : 0);
					$total_cost_list[] = (!empty($request->permission) ? $request->permission : 0);
					$total_cost_list[] = (!empty($request->bus) ? $request->bus : 0);
					$total_cost_list[] = (!empty($request->sipa) ? $request->sipa : 0);
					$total_cost_list[] = (!empty($request->taxibike) ? $request->taxibike : 0);
					$total_cost_list[] = (!empty($request->rickshaw) ? $request->rickshaw : 0);
					$total_cost_list[] = (!empty($request->taxi) ? $request->taxi : 0);
					$total_cost_list[] = (!empty($request->other_cost) ? $request->other_cost : 0);
					
					$total = array_sum($total_cost_list);

				$insert = CashAdvance::create([
					'id_employee'		=> $user->id,
					'id_area'			=> $request->area,
					'date'              => $request->date,
					'description'       => $request->description ?? null,
					'km_begin'          => $request->km_begin ?? null,
					'km_end'            => $request->km_end ?? null,
					'km_distance'       => (!empty($request->km_end) && !empty($request->km_begin)) ? $request->km_end - $request->km_begin : null,
					'tpd'               => $request->tpd ?? null,
					'hotel'             => $request->hotel ?? null,
					'bbm'               => $request->bbm ?? null,
					'parking_and_toll'  => $request->parking_and_toll ?? null,
					'raw_material'      => $request->raw_material ?? null,
					'property'          => $request->property ?? null,
					'permission'        => $request->permission ?? null,
					'bus'               => $request->bus ?? null,
					'sipa'              => $request->sipa ?? null,
					'taxibike'          => $request->taxibike ?? null,
					'rickshaw'          => $request->rickshaw ?? null,
					'taxi'              => $request->taxi ?? null,
					'other_cost'    	=> $request->other_cost ?? null,
					'other_description' => $request->other_description ?? null,
					'total_cost'        => $total,
				]);
				if ($insert->id) {
					$res['success'] = true;
					$res['msg'] 	= "Success add Cash Advance.";
				} else {
					$res['success'] = false;
					$res['msg'] 	= "Failed to add Cash Advance.";
				}
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
	
	public function list()
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;
			
			$cash = CashAdvance::where('id_employee', $user->id)->orderBy('id','desc')->get();
			if ($cash->count() > 0) {
				$listCash = [];
				$res['success'] = true;
				foreach ($cash as $data) {
					$listCash[] = array(
						'id'				=> $data->id,
						'id_area'			=> $data->id_area,
						'date'              => $data->date,
						'description'       => $data->description ?? null,
						'km_begin'          => $data->km_begin ?? null,
						'km_end'            => $data->km_end ?? null,
						'km_distance'       => $data->km_distance ?? null,
						'tpd'               => $data->tpd ?? null,
						'hotel'             => $data->hotel ?? null,
						'bbm'               => $data->bbm ?? null,
						'parking_and_toll'  => $data->parking_and_toll ?? null,
						'raw_material'      => $data->raw_material ?? null,
						'property'          => $data->property ?? null,
						'permission'        => $data->permission ?? null,
						'bus'               => $data->bus ?? null,
						'sipa'              => $data->sipa ?? null,
						'taxibike'          => $data->taxibike ?? null,
						'rickshaw'          => $data->rickshaw ?? null,
						'taxi'              => $data->taxi ?? null,
						'other_cost'    	=> $data->other_cost ?? null,
						'other_description' => $data->other_description ?? null,
						'total_cost'        => $data->total_cost ?? null,
						'area_name'			=> $data->area->name ?? null,
					);
				}
				$res['cash_advance'] = $listCash;
			} else {
				$res['success'] = false;
				$res['msg'] = "Kamu tidak mempunyai Cash Advance.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}