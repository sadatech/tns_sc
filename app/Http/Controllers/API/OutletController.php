<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Cbd;
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
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function store(Request $request)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$data = $request->all();
				if (empty($data['phone']) || empty($data['pasar']) ) {
					$res['success'] = false;
					$res['msg'] = "Data cannot be empty.";
					$code = 200;
				} else {
					$emp = EmployeePasar::where([
						'id_pasar' => $data['pasar'],
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
							'customer_code'		=> $data['code'],
							'name'				=> $data['name'],
							'phone'				=> $data['phone'],
							'address'			=> $data['address'],
							'new_ro'			=> ($data['new_ro'] == 'yes' ? Carbon::today()->toDateString() : '' ),
							'active'			=> 1,
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
					} else {
						$res['success'] = false;
						$res['msg'] = "Pasar tidak bisa ditermukan.";
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

	public function update(Request $request, $id)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$data = $request->all();
				$update = Outlet::where('id',$id)->update([
					'customer_code'		=> $data['code'],
					'name'				=> $data['name'],
					'phone'				=> $data['phone'],
					'address'			=> $data['address'],
				]);
				if ($update) {
					$res['success'] = true;
					$res['msg'] = "Success update outlets.";
					$code = 200;
				} else {
					$res['success'] = false;
					$res['msg'] = "Failed to update outlets.";
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

	public function list($active = 1)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				if ($active == 1 || $active == 2) {
					if ($active == 1) {
						$activeStatus = "aktif";
					}else{
						$activeStatus = "tidak aktif";
					}
					$outlet = Outlet::where('active', $active)->whereHas('employeePasar', function($query) use ($user) {
						return $query->where('id_employee', $user->id);
					})->get();
					$code = 200;
					if ($outlet->count() > 0) {
						$listOutlet = [];
						$res['success'] = true;
						foreach ($outlet as $data) {
							$listOutlet[] = array(
								'id' 		=> $data->id,
								'name' 		=> $data->name,
								'code' 		=> $data->customer_code,
								'phone' 	=> $data->phone,
								'address'	=> $data->address,
								'new_ro'	=> $data->new_ro,
								'id_pasar' 	=> $data->employeePasar->pasar->id,
								'pasar' 	=> $data->employeePasar->pasar->name,
								'address' 	=> $data->employeePasar->pasar->address,
							);
						}
						$res['outlet'] = $listOutlet;
					} else {
						$res['success'] = false;
						$res['msg'] = "Kamu tidak mempunyai outlet $activeStatus.";
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
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
			} else {
				$code 	= 200;
				$attId 	= Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])->whereDate('date', '=', Carbon::today()->toDateString())->orderBy('created_at', 'DESC')->first();
				if (isset($attId->id)) {
					$attendance = AttendanceOutlet::where(['id_attendance' => $attId->id])->first();
					if (!empty($attendance)) {
						if ($attendance->checkout == null) {
							$res['success'] = true;
							$res['msg'] 	= "Kamu belum checkout di outlet sebelumnya.";
							$res['id'] 		= (isset($attendance->id_outlet) ? $attendance->id_outlet : null);
							$res['name'] 	= (isset($attendance->outlet->name) ? $attendance->outlet->name : null);
							$res['time'] 	= $attendance->checkin;

							$cbd = Cbd::where('id_outlet',$attendance->id_outlet)->where('id_employee',$user->id)->whereDate('date', '=', Carbon::today()->toDateString())->count();
							$res['cbd'] 	= ($cbd > 0 ? 'true' : 'false');
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

	public function disable($id, $status)
	{
		try {
			if (!$user = JWTAuth::parseToken()->authenticate()) {
				$res['msg'] = "User not found.";
				$code = $e->getStatusCode();
			} else {
				$code = 200;
				if (empty($id)) {
					$res['success'] = false;
					$res['msg'] = "Please select store.";
				} else if( empty($status) ) {
					$res['success'] = false;
					$res['msg'] = "Please set store status.";
				} else {
					DB::transaction(function() use ($id, $status, &$res){
						if ($status == 'true') {
							$update = Outlet::where("id", $id)
							->update([
								'active'	=> 2,
							]);
							$disableStatus = 'disable';
						} else {
							$update = Outlet::where("id", $id)
							->update([
								'active'	=> 1,
							]);
							$disableStatus = 'enable';
						}
						if ($update) {
							$res['success'] = true;
							$res['msg'] = "Success $disableStatus outlets.";
						}else{
							$res['success'] = false;
							$res['msg'] = "Fail $disableStatus outlets.";
						}
					});
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
