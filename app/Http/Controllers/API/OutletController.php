<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Outlet;
use App\EmployeePasar;
use App\AttendanceOutlet;
use App\Attendance;
use Carbon\Carbon;
use JWTAuth;
use Config;
use DB;

class OutletController extends Controller
{
	public function store(Request $request)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$data = json_decode($request->getContent());
				if (empty($data->pasar) || empty($data->outlet)) {
					$res['success'] = false;
					$res['msg'] = "Data cannot be empty.";
					$code = 200;
				} else {
					$emp = EmployeePasar::where([
						'id_pasar' => $request->input('pasar'),
						'id_employee' => $user->id
					])->first();
					if (!empty($emp)) {
						// $outlets = array();
						// foreach ($data->outlet as $data) {
						// 	$outlets[] = array(
						// 		'id_employee_pasar'	=> $emp->id,
						// 		'name'				=> $data->name,
						// 		'phone'				=> $data->phone,
						// 		'active'			=> true,
						// 	);
						// }
						// $insert = DB::table('outlets')->insert($outlets);
						$insert = Outlet::create([
							'id_employee_pasar'	=> $emp->id,
							'name'				=> $request->input('name'),
							'phone'				=> $$request->input('phone'),
							'active'			=> true,
						]);
						if ($insert->id) {
							$res['success'] = true;
							$res['msg'] = "Success add outlets.";
							$code = 200;
						} else {
							$res['success'] = false;
							$res['msg'] = "Failed to add outlets.";
							$code = 200;
						}
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

	public function list($id = 1)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				if ($id == 1) {
					$outlet = Outlet::where('active', true)->with(['employeePasar' => function($query) use ($user) {
						$query->where([
							'id_employee' => $user->id
						]);
					}]);
					if ($outlet->count() < 1) {
						$res['success'] = false;
						$res['msg'] = "Kamu tidak mempunyai outlet aktif.";
						$code = 200;
					} else {
						$res['success'] = true;
						$res['outlet'] = $outlet->get(['id','name','phone']);
						$code = 200;
					}
				} else if ($id == 2) {
					$outlet = Outlet::where('active', false)->with(['employeePasar' => function($query) use ($user) {
						$query->where([
							'id_employee' => $user->id
						]);
					}])->get();
					if ($outlet->count() < 1) {
						$res['success'] = false;
						$res['msg'] = "Kamu tidak mempunyai outlet tidak aktif.";
						$code = 200;
					} else {
						$res['success'] = true;
						$res['outlet'] = $outlet->get(['id','name','phone']);
						$code = 200;
					}
				} else {
					$res['success'] = false;
					$res['msg'] = "Type outlet tidak diketahui.";
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

	public function checkin(Request $request)
	{
		try {
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$attendance = AttendanceOutlet::where([
					'id_outlet' => $request->input('outlet')
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
						$insertAtt = AttendanceOutlet::create([
							'id_attendance' => $insert->id,
							'id_outlet' => $request->input('outlet'),
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
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$attId = Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])
				->whereDate('date', '=', Carbon::today()->toDateString())->first();
				if (isset($attId->id)) {
					$absen = AttendanceOutlet::where(['id_attendance' => $attId->id])->first();
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
			Config::set('auth.providers.users.model', \App\Employee::class);
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
			} else {
				$attId = Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])->whereDate('date', '=', Carbon::today()->toDateString())->first();
				if (isset($attId->id)) {
					$attendance = AttendanceOutlet::where(['id_attendance' => $attId->id])->first();
					if (!empty($attendance)) {
						if ($attendance->checkout == null) {
							$res['success'] = true;
							$res['msg'] = "Kamu belum checkout di outlet sebelumnya.";
							$res['id'] = (isset($attendance->id_outlet) ? $attendance->id_outlet : null);
							$res['name'] = (isset($attendance->outlet->name) ? $attendance->outlet->name : null);
							$res['time'] = $attendance->checkin;
							$code = 200;
						} else {
							$res['success'] = false;
							$res['msg'] = "Sudah melakukan checkout.";
							$code = 200;
						}
					} else {
						$res['success'] = false;
						$res['msg'] = "Belum melakukan check-in.";
						$code = 200;
					}
				} else {
					$res['success'] = false;
					$res['msg'] = "Belum melakukan check-in.";
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
