<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\Attendance;
use App\AttendanceDetail;
use App\AttendanceOutlet;
use App\AttendancePasar;
use App\Cbd;
use Carbon\Carbon;
use JWTFactory;
use JWTAuth;
use Config;

class AttendanceController extends Controller
{
	use ApiAuthHelper;

	public function absen(Request $request, $type = 'MTC')
	{
		$check = $this->authCheck();

		if ($check['success'] == true) {
			
			$user = $check['user'];
			$res['code'] = 200;

			if ($request->input('keterangan') == 'Check-in') {
				if (strtoupper($type) == 'MTC') {
					$attendance = AttendanceDetail::where([
						'id_store' => $request->input('store'),
						'id_place' => $request->input('place')
					]);
				}else if (strtoupper($type) == 'GTC-MD') {
					$attendance = AttendanceOutlet::where([
						'id_outlet' => $request->input('outlet')
					]);		
				}else if (strtoupper($type) == 'GTC-SPG') {
					$attendance = AttendancePasar::where([
						'id_pasar' => $request->input('pasar')
					]);			
				}
				$attendance->with(['attendance' => function($query) use ($user) {
					$query->where([
						'id_employee' => $user->id,
						'keterangan' => 'Check-in',
					])->whereDate('date', '=', Carbon::today()->toDateString());
				}]);

				if ($attendance->count() > 0) {
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
						if (strtoupper($type) == 'MTC') {
							$insertAtt = AttendanceDetail::create([
								'id_attendance' => $insert->id,
								'id_store' 		=> $request->input('store'),
								'id_place' 		=> $request->input('place'),
								'checkin' 		=> Carbon::now()
							]);
						}else if (strtoupper($type) == 'GTC-MD') {
							$insertAtt = AttendanceOutlet::create([
								'id_attendance' => $insert->id,
								'id_outlet' 	=> $request->input('outlet'),
								'checkin' 		=> Carbon::now()
							]);
						}else if (strtoupper($type) == 'GTC-SPG') {
							$insertAtt = AttendancePasar::create([
								'id_attendance' => $insert->id,
								'id_pasar' 		=> $request->input('pasar'),
								'checkin' 		=> Carbon::now()
							]);
						}

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
					$res['msg'] = "Kamu sudah melakukan absen hari ini.";
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
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}

	public function checkout(Request $request, $type = 'MTC')
	{
		$check = $this->authCheck();

		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$attId = Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])
			->whereDate('date', '=', Carbon::today()->toDateString())->first();
			if (isset($attId->id)) {

				if (strtoupper($type) == 'MTC') {
					$absen = AttendanceDetail::where(['id_attendance' => $attId->id])->first();
				}else if (strtoupper($type) == 'GTC-MD') {
					$absen = AttendanceOutlet::where(['id_attendance' => $attId->id])->first();
				}else if (strtoupper($type) == 'GTC-SPG') {
					$absen = AttendancePasar::where(['id_attendance' => $attId->id])->first();
				}

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
			}

		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res, $code);
	}

	public function status($type = 'MTC')
	{
		$check = $this->authCheck();

		if ($check['success'] == true) {

			$user = $check['user'];
			$res['code'] = 200;

			$attId = Attendance::where(['id_employee' => $user->id, 'keterangan' => 'Check-in'])->whereDate('date', '=', Carbon::today()->toDateString())->orderBy('created_at', 'DESC')->first();
			if (isset($attId->id)) {
				if (strtoupper($type) == 'MTC') {
					$attendance = AttendanceDetail::where(['id_attendance' => $attId->id])->first();
					$lokasi = 'toko';
				}else if (strtoupper($type) == 'GTC-MD') {
					$attendance = AttendanceOutlet::where(['id_attendance' => $attId->id])->first();
					$lokasi = 'outlet';
				}else if (strtoupper($type) == 'GTC-SPG') {
					$attendance = AttendancePasar::where(['id_attendance' => $attId->id])->first();
					$lokasi = 'pasar';
				}

				if (!empty($attendance)) {
					$code 			= 200;
					$res['success'] = true;
					$res['msg'] 	= "Kamu belum checkout di $lokasi sebelumnya.";

					if ($attendance->checkout == null) {
						if (strtoupper($type) == 'MTC') {
							$res['id_store'] 	= (isset($attendance->id_store) ? $attendance->id_store : null);
							$res['store_name'] 	= (isset($attendance->store->name1) ? $attendance->store->name1 : null);
							$res['id_place'] 	= (isset($attendance->id_place) ? $attendance->id_place : null);
							$res['place_name'] 	= (isset($attendance->place->name) ? $place = $attendance->place->name : null );
							$res['time'] 		= $attendance->checkin;
						}else if (strtoupper($type) == 'GTC-MD') {
							$res['id_outlet'] 	= (isset($attendance->id_outlet) ? $attendance->id_outlet : null);
							$res['name'] 		= (isset($attendance->outlet->name) ? $attendance->outlet->name : null);
							$res['time'] 		= $attendance->checkin;

							$cbd 				= Cbd::where('id_outlet',$attendance->id_outlet)->where('id_employee',$user->id)->whereDate('date', '=', Carbon::today()->toDateString())->count();
							$res['cbd'] 		= ($cbd > 0 ? 'true' : 'false');
						}else if (strtoupper($type) == 'GTC-SPG') {
							$res['id_pasar']	= (isset($attendance->id_pasar) ? $attendance->id_pasar : null);
							$res['name'] 		= (isset($attendance->pasar->name) ? $attendance->pasar->name : null);
							$res['time'] 		= $attendance->checkin;
						}
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
		}else{
			$res = $check;
		}

		$code = $res['code'];
		unset($res['code']);

		return response()->json($res, $code);
	}
}
