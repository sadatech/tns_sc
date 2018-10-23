<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Attendance;
use App\AttendanceDetail;
use Carbon\Carbon;
use JWTFactory;
use JWTAuth;
use Config;

class AttendanceController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}
	
	public function absen(Request $request)
	{
		try {
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['success'] = false;
					$res['msg'] = "User not found.";
					$code = 200;
				} else {
					if ($request->input('keterangan') == 'Check-in') {
						$attendance = AttendanceDetail::where([
							'id_store' => $request->input('store'),
							'id_place' => $request->input('place')
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
								$insertAtt = AttendanceDetail::create([
									'id_attendance' => $insert->id,
									'id_store' => $request->input('store'),
									'id_place' => $request->input('place'),
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
					} else {
						$att = Attendance::where([
							'id_employee' => $user->id,
							'keterangan' => 'Check-in'
						])
						->orWhere('keterangan', '=', 'Cuti')
						->orWhere('keterangan', '=', 'Off')
						->orWhere('keterangan', '=', 'Sakit')
						->whereDate('date', '=', Carbon::today()->toDateString())->first();
						if (isset($att->id)) {
							$res['success'] = false;
							$res['msg'] = "Kamu sudah melakukan absen hari ini. Absen ".$request->input('keterangan')." Anda tidak terhitung.";
							$code = 200;
						} else {
							$insert = Attendance::create([
								'id_employee' => $user->id,
								'keterangan' => $request->input('keterangan'),
								'date' => Carbon::now()
							]);
							if ($insert) {
								$res['success'] = true;
								$res['msg'] = "Berhasil";
								$code = 200;
							} else {
								$res['success'] = false;
								$res['msg'] = "Gagal melakukan absensi.";
								$code = 200;
							}
						}
					}
				}
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$res['success'] = false;
			$res['msg'] = "Token Expired.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			$res['success'] = false;
			$res['msg'] = "Token Invalid.";
			$code = $e->getStatusCode();
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
			$res['success'] = false;
			$res['msg'] = "Token Absent.";
			$code = $e->getStatusCode();
		}
		return response()->json($res, $code);
	}

	public function checkout(Request $request)
	{
		try {
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
					$code = $e->getStatusCode();
				} else {
					$attId = Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])
					->whereDate('date', '=', Carbon::today()->toDateString())->pluck('id');
					if (isset($attId)) {
						$absen = AttendanceDetail::whereIn('id_attendance', $attId)->whereNull('checkout')->orderBy('id','desc')->first();
						if (isset($absen)) {
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
							$res['msg'] = "Sudah melakukan checkout untuk semua tempat.";
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

	public function status()
	{
		try {
			if (JWTAuth::getToken() != null) {
				if (!$user = JWTAuth::parseToken()->authenticate()) {
					$res['msg'] = "User not found.";
				} else {
					$attId = Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])->whereDate('date', '=', Carbon::today()->toDateString())->first();
					if (isset($attId->id)) {
						$attendance = AttendanceDetail::where(['id_attendance' => $attId->id])->first();
						if (!empty($attendance)) {
							if ($attendance->checkout == null) {
								$res['success'] = true;
								$res['msg'] = "Kamu belum checkout ditoko sebelumnya.";

								if (isset($attendance->id_store)) {
									$id_store = $attendance->id_store;
								} else {
									$id_store = null;
								} 
								if (isset($attendance->store->name1)) {
									$store = $attendance->store->name1;
								} else {
									$store = null;
								}
								if (isset($attendance->id_place)) {
									$id_place = $attendance->id_place;
								} else {
									$id_place = null;
								}
								if (isset($attendance->place->name)) {
									$place = $attendance->place->name;
								} else {
									$place = null;
								}

								$res['id_store'] = $id_store;
								$res['store_name'] = $store;
								$res['id_place'] = $id_place;
								$res['place_name'] = $place;
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
