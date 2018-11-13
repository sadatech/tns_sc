<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Pasar;
use App\Attendance;
use Carbon\Carbon;
use App\AttendancePasar;
use App\EmployeePasar;
use JWTAuth;
use Config;
use DB;

class PasarController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list()
	{
		try {
			$res['success'] = false;
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					$store = EmployeePasar::where([
						'id_employee' => $user->id
					])->with('pasar.subarea')->get(['id_pasar']);
					if (!$store->isEmpty()) {
						$storeArr = array();
						foreach ($store as $key => $value) {
							$storeArr[$key] = array(
								'id' => $value->pasar->id,
								'name' => $value->pasar->name,
								'address' => $value->pasar->address,
								'latitude' => (isset($value->pasar->latitude) ? $value->pasar->latitude : ""),
								'longitude' => (isset($value->pasar->longitude) ? $value->pasar->longitude : ""),
								'subarea' => $value->pasar->subarea->name
							);
						}
						$res['success'] = true;
						$res['pasar'] = $storeArr;
					} else {
						$res['msg'] = "Pasar not found.";
					}
				}
			}else{
				$res['msg'] = "User not found.";
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
		return response()->json($res, 200);
	}

	public function checkin(Request $request)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$attendance = AttendancePasar::where([
					'id_pasar' => $request->input('pasar')
				])->whereDate('created_at', '=', Carbon::today()->toDateString())
				->with(['attendance' => function($query) use ($user) {
					$query->where([
						'id_employee' => $user->id,
						'keterangan' => 'Check-in',
					]);
				}])->count();
				if ($attendance > 0) {
					$res['success'] = false;
					$res['msg'] = "Sudah melakukan absensi di tempat yang sama.";
					$code = 200;
				} else {
					$insert = Attendance::create([
						'id_employee' => $user->id,
						'keterangan' => $request->input('keterangan'),
						'date' => Carbon::now()
					]);
					if ($insert) {	
						$insertAtt = AttendancePasar::create([
							'id_attendance' => $insert->id,
							'id_pasar' => $request->input('pasar'),
							'checkin' => Carbon::now()
						]);
						if ($insertAtt) {
							$res['success'] = true;
							$res['msg'] = "Berhasil melakukan absensi.";
							$code = 200;
						} else {
							$res['success'] = false;
							$res['msg'] = "Gagal melakukan absensi.";
							$code = 200;
						}
					} else {
						$res['success'] = false;
						$res['msg'] = "Gagal melakukan absensi.";
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

	public function checkout()
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$attId = Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])
				->whereDate('date', '=', Carbon::today()->toDateString())->first();
				if (isset($attId->id)) {
					$absen = AttendancePasar::where(['id_attendance' => $attId->id])->first();
					if (isset($absen->checkout) == null) {
						$absen->checkout = Carbon::now();
						if ($absen->save()) {
							$res['success'] = true;
							$res['msg'] = "Berhasil melakukan check-out.";
							$code = 200;
						} else {
							$res['success'] = false;
							$res['msg'] = "Gagal melakukan check-out.";
							$code = 200;
						}
					} else {
						$res['success'] = false;
						$res['msg'] = "Sudah melakukan checkout untuk hari ini.";
						$code = 200;
					}
				} else {
					$res['success'] = false;
					$res['msg'] = "Kamu belum melakukan check-in.";
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

	public function status()
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
			} else {
				$code 	= 200;
				$attId 	= Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])->whereDate('date', '=', Carbon::today()->toDateString())->first();
				if (isset($attId->id)) {
					$attendance = AttendancePasar::where(['id_attendance' => $attId->id])->first();
					if (!empty($attendance)) {
						if ($attendance->checkout == null) {
							$res['success'] = true;
							$res['msg'] 	= "Kamu belum checkout di pasar sebelumnya.";
							$res['id'] 		= (isset($attendance->id_pasar) ? $attendance->id_pasar : null);
							$res['name'] 	= (isset($attendance->pasar->name) ? $attendance->pasar->name : null);
							$res['time'] 	= $attendance->checkin;
						} else {
							$res['success'] = false;
							$res['msg'] = "Sudah melakukan checkout.";
						}
					} else {
						$res['success'] = false;
						$res['msg'] = "Belum melakukan check-in.";
					}
				} else {
					$res['success'] = false;
					$res['msg'] = "Belum melakukan check-in.";
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
