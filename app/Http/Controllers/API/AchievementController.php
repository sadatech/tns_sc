<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Config;
use Carbon\Carbon;
use App\Model\Extend\TargetKpiMd;
use App\TargetGtc;
use App\EmployeeStore;
use App\Employee;

class AchievementController extends Controller
{
    public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

    // MD PASAR (PARAM & TOKEN)
    public function MDPasar($id_employee = ''){

    	try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {

					$result = array();

#			    	$periode = Carbon::parse('November 2018');
			    	$periode = Carbon::now();

			    	$id = ($id_employee == '') ? $user->id : $id_employee;

			    	// return $id;

			    	$employee = TargetKpiMd::where('id', $id)->first();

			    	$targets = $employee->getTarget($periode);
			    	$sales_value = $employee->getSalesValue($periode);
			    	$ec = $employee->getEc($periode);
			    	$cbd = $employee->getCbd($periode);

		    		$result['target_hk'] = (is_null($targets)) ? 0 : $targets['hk'];
		    		$result['target_sales_value'] = (is_null($targets)) ? 0 : $targets['value_sales'];
		    		$result['target_ec_pf'] = (is_null($targets)) ? 0 : $targets['ec'];
		    		$result['target_cbd'] = (is_null($targets)) ? 0 : $targets['cbd'];
		    		$result['ach_sales_value'] = (is_null($sales_value)) ? 0 : $sales_value;
		    		$result['ach_ec_pf'] = (is_null($ec)) ? 0 : $ec;
		    		$result['ach_cbd'] = (is_null($cbd)) ? 0 : $cbd;

			    	$res['success'] = true;
			    	$res['achievement'] = $result;
				}
			}else{
				$res['msg'] = "User not found.";
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
		}
		return response()->json($res);


    }

    // SPG MTC (PARAM & TOKEN)
    public function MtcEmployee($id_employee = ''){

    	try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {

					$result = array();

#			    	$periode = Carbon::parse('November 2018');
                    $periode = Carbon::now();

			    	$id = ($id_employee == '') ? $user->id : $id_employee;

			    	// return $user;

			    	// $id_stores = EmployeeStore::where('id_employee', $id)->pluck('id_store')->toArray();

			    	// $result['actual_previous'] = 0;
			    	// $result['actual_current'] = 0;
			    	// $result['target'] = 0;
			    	// $result['achievement'] = 0;
			    	// $result['target_focus1'] = 0;
			    	// $result['achievement_focus1'] = 0;
			    	// $result['percentage_focus1'] = 0;
			    	// $result['target_focus2'] = 0;
			    	// $result['achievement_focus2'] = 0;
			    	// $result['percentage_focus2'] = 0;
			    	// $result['growth'] = 0;

			    	// foreach ($id_stores as $id_store) {
			    		$result['actual_previous'] = $user->getActualPreviousApi(['date' => $periode]);
			    		$result['actual_current'] = $user->getActualApi(['date' => $periode]);
			    		$result['target'] = $user->getTargetApi(['date' => $periode]);
			    		$result['target_focus1'] = $user->getTarget1AltApi(['date' => $periode]);
			    		$result['achievement_focus1'] = $user->getActualPf1Api(['date' => $periode]);
			    		$result['target_focus2'] = $user->getTarget2AltApi(['date' => $periode]);
			    		$result['achievement_focus2'] = $user->getActualPf2Api(['date' => $periode]);
			    	// }

			    	$result['achievement'] = ($result['target'] > 0) ? round(($result['actual_current']/$result['target'])*100, 2).'%' : '0%';;
			    	$result['growth'] = ($result['actual_previous'] > 0) ? round((($result['actual_current']/$result['actual_previous'])-1)*100, 2).'%' : '0%';
			    	$result['percentage_focus1'] = ($result['target_focus1'] > 0) ? round(($result['achievement_focus1']/$result['target_focus1'])*100, 2).'%' : '0%';
			    	$result['percentage_focus2'] = ($result['target_focus2'] > 0) ? round(($result['achievement_focus2']/$result['target_focus2'])*100, 2).'%' : '0%';

			    	$res['success'] = true;
			    	$res['achievement'] = $result;
				}
			}else{
				$res['msg'] = "User not found.";
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['msg'] = "Token Expired.";

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['msg'] = "Token Invalid.";

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['msg'] = "Token Absent.";
		}
		return response()->json($res);


    }
}
