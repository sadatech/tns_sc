<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use App\Position;
use App\Agency;
use App\SubArea;
use App\Store;
use App\Timezone;
use App\Employee;
use App\EmployeePasar;
use App\EmployeeSubArea;
use App\Pasar;
use App\EmployeeStore;
use App\Filters\EmployeeFilters;

class EmployeeController extends Controller
{
	public function getDataWithFilters(EmployeeFilters $filters){
		$data = Employee::filter($filters)->get();
		return $data;
	}

	public function baca()
	{
		return view('employee.employee');
	}

	public function read()
	{
		$data['timezone'] 	= Timezone::all();
		$data['position'] 	= Position::get();
		$data['agency'] 	= Agency::get();
		$data['store'] 		= Store::get();
		$data['pasar'] 		= Pasar::get();
		$data['subarea'] 	= SubArea::get();
		return view('employee.employeecreate', $data);
	}
	public function readupdate($id)
	{
		$data['timezone'] 	= Timezone::all();
		$data['emp'] 		= Employee::where(['id' => $id])->first();
		$data['position'] 	= Position::get();
		$data['agency'] 	= Agency::get();
		$data['store'] 		= Store::get();
		$data['pasar'] 		= Pasar::get();
		$data['subarea'] 	= SubArea::get();
		$data['store_selected'] = json_encode(EmployeeStore::where(['employee_stores.id_employee' => $id])->join('stores','stores.id','employee_stores.id_store')->select(DB::raw("concat(stores.id,'|',stores.name1) as stores_item"))->get()->toArray());
		$data['pasar_selected'] = json_encode(EmployeePasar::where(['employee_pasars.id_employee' => $id])->join('pasars','pasars.id','employee_pasars.id_pasar')->select(DB::raw("concat(pasars.id,'|',pasars.name) as pasars_item"))->get()->toArray());
		if ($data['emp']->isResign) {
			return redirect()->route('employee');
		} else {
			return view('employee.employeeupdate', $data);
		}
	}				

	public function store(Request $request)
	{
		$data=$request->all();
		$limit=[
			'foto_ktp' 		=> 'max:10000|required|mimes:jpeg,jpg,bmp,png',
			'foto_tabungan' => 'max:10000|mimes:jpeg,jpg,bmp,png',
			'name' 			=> 'required',
			'password' 		=> 'required',
			'position' 		=> 'required',
			'agency' 		=> 'required|numeric',
			'email' 		=> 'email|required',
			'phone' 		=> 'required|numeric|unique:employees',
			'nik' 			=> 'required|unique:employees',
			'ktp' 			=> 'required|numeric|unique:employees',
			'gender' 		=> 'required',
			'education' 	=> 'required',
			'birthdate' 	=> 'required|date',
			'timezone'		=> 'required',
			'joinAt'		=> 'required'
		];
		$validator = Validator($data, $limit);
		if ($validator->fails()){
			return redirect()->back()
			->withErrors($validator)
			->withInput();
		} else {
			$ktp = $data['foto_ktp'];
			$foto_ktp = Str::random().time()."_".rand(1,99999).".".$ktp->getClientOriginalExtension();
			$ktp_path = 'uploads/ktp';
			$ktp->move($ktp_path, $foto_ktp);
			if ($request->file('foto_tabungan'))
			{
				$tabungan = $data['foto_tabungan'];
				$foto_tabungan = Str::random().time()."_".rand(1,99999).".".$tabungan->getClientOriginalExtension();
				$tabungan_path = 'uploads/tabungan';
				$tabungan->move($tabungan_path, $foto_tabungan);
			} else {
				$foto_tabungan = "default.png";
			}
			if ($request->file('foto_profile'))
			{
				$tabungan = $data['foto_profile'];
				$foto_profile = Str::random().time()."_".rand(1,99999).".".$tabungan->getClientOriginalExtension();
				$tabungan_path = 'uploads/profile';
				$tabungan->move($tabungan_path, $foto_profile);
			} else {
				$foto_profile = "default.png";
			}
			if ($request->input('status') == null) {
				$status = null;
			} else {
				$status = $request->input('status');
			}
			if (Position::where('id', $request->input('position'))->count() > 0) {
				$insert = Employee::create([
					'name' 			=> $request->input('name'),
					'password' 		=> bcrypt($request->input('password')),
					'nik' 			=> $request->input('nik'),
					'ktp' 			=> $request->input('ktp'),
					'phone' 		=> $request->input('phone'),
					'email' 		=> $request->input('email'),
					'rekening' 		=> $request->input('rekening'),
					'bank' 			=> $request->input('bank'),
					'education' 	=> $request->input('education'),
					'birthdate' 	=> $request->input('birthdate'),
					'gender' 		=> $request->input('gender'),
					'status' 		=> $status,
					'joinAt' 		=> $request->input('joinAt'),
					'foto_ktp' 		=> $foto_ktp,
					'foto_tabungan' => $foto_tabungan,
					'foto_profile' => $foto_profile,
					'id_position' 	=> $request->input('position'),
					'id_timezone' 	=> $request->input('timezone'),
					'id_agency' 	=> $request->input('agency')
				]);
				if ($insert->id) {
					if ($request->input('status') == 'Stay') {
						EmployeeStore::create([
							'id_store' 		=> $request->input('store'),
							'id_employee' 	=> $insert->id,
						]);
						return redirect()->route('employee')
						->with([
							'type' 		=> 'success',
							'title' 	=> 'Sukses!<br/>',
							'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
						]);
					} else if($request->input('status') == 'Mobile') {
						$dataStore = array();
						foreach ($request->input('stores') as $store) {
							$dataStore[] = array(
								'id_employee' 	=> $insert->id,
								'id_store' 		=> $store,
							);
						}
						DB::table('employee_stores')->insert($dataStore);
						return redirect()->route('employee')
						->with([
							'type' 		=> 'success',
							'title' 	=> 'Sukses!<br/>',
							'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
						]);
					} else if (!empty($request->input('pasar'))) {
						$dataPasar = array();
						foreach ($request->input('pasar') as $pasar) {
							$dataPasar[] = array(
								'id_employee' 	=> $insert->id,
								'id_pasar' 		=> $pasar,
							);
						}
						DB::table('employee_pasars')->insert($dataPasar);
						return redirect()->route('employee.pasar')
						->with([
							'type' 		=> 'success',
							'title' 	=> 'Sukses!<br/>',
							'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
						]);
					} else if (!empty($request->input('subarea'))) {
						$dataSubArea = array();
						foreach ($request->input('subarea') as $subarea) {
							$dataSubArea[] = array(
								'id_employee' 	=> $insert->id,
								'id_subarea' 	=> $subarea,
							);
						}
						DB::table('employee_sub_areas')->insert($dataSubArea);
						return redirect()->route('employee.pasar')
						->with([
							'type' 		=> 'success',
							'title' 	=> 'Sukses!<br/>',
							'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
						]);
					}
				}
			} else {
				return redirect()->route('employee')
				->with([
					'type' 		=> 'danger',
					'title' 	=> 'Terjadi Kesalahan!<br/>',
					'message'	=> '<i class="em em-thinking_face mr-2"></i>Position tidak tersedia!'
				]);
			}
		}
	}  

	public function data()
	{
		$employee = Employee::where(['isResign' => false])
		->whereIn('id_position', [1,2,6])
		->with(['agency', 'position', 'employeeStore', 'timezone'])
		->select('employees.*');
		return Datatables::of($employee)
		->addColumn('action', function ($employee) {
			$employeeS = EmployeeStore::where(['id_employee' => $employee->id])->get();
			if ($employee->status = "Stay" && (!empty($employeeS->store)) && (!empty($employeeS->coverage)) && (!empty($employeeS->is_vito)) && (!empty($employeeS->address))) {
				$store = $employeeS->store->id;
				$coverage = $employeeS->store->id;
				$is_vito = $employeeS->store->id;
				$address = $employeeS->store->id;
			} else {
				$store = array();
				$coverage = array();
				foreach ($employeeS as $data) {
					$store[] = $data->store->name1;
					$coverage[] = $data->store->coverage;
					$is_vito[] = $data->store->is_vito;
					$address[] = $data->store->address;
				}
			}
			$data = array(
				'id'        	=> $employee->id,
				'store'    		=> rtrim(implode(', ', $store), ','),
				'coverage'		=> rtrim(implode(', ', $coverage), ','),
				'is_vito'		=> rtrim(implode(', ', $is_vito), ','),
				'address'		=> rtrim(implode(', ', $address), ','),
			);
			return "<a href=".route('ubah.employee', $employee->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
			<button data-url=".route('employee.delete', $employee->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
			<button onclick='viewModal(".json_encode($data).")' class='btn btn-sm btn-warning btn-square' title='View Store'><i class='si si-picture mr-2'></i> STORE</button>
			<a href=".asset('/uploads/ktp')."/".$employee->foto_ktp." class='btn btn-sm btn-success btn-square popup-image' title='Show Photo KTP'><i class='si si-picture mr-2'></i> KTP</a>
			<a href=".asset('/uploads/tabungan')."/".$employee->foto_tabungan." class='btn btn-sm btn-info btn-square popup-image' title='Show Photo Tabungan'><i class='si si-picture mr-2'></i> TABUNGAN</a>";
		})
		->addColumn('employeeStore', function($employee) {
			$store = EmployeeStore::where(['id_employee' => $employee->id])->get();
			$storeList = array();
			foreach ($store as $data) {
				$storeList[] = $data->store->name1;
			}
			return rtrim(implode(',', $storeList), ',');
		})
		->addColumn('position', function($employee) {
			return $employee->position->name;
		})
		->addColumn('timezone', function($employee) {
			return $employee->timezone->name;
		})
		->addColumn('agency', function($employee) {
			return $employee->agency->name;
		})->make(true);
	}

	public function update(Request $request, $id) 
	{
		$data=$request->all();
		$limit=[
			'name'    				=> 	'required',
			'gender' 				=> 	'required',
			'education'           	=> 	'required',
			'birthdate'				=> 	'required',
			'email'      			=> 	'required|email',
			'nik'    				=> 	'required|numeric',
			'ktp'        			=> 	'required|numeric',
			'phone'       			=> 	'required|numeric',
			'agency'				=> 	'required|numeric',
			'position'				=> 	'required',
		];
		$validator = Validator($data, $limit);
		if ($validator->fails()){
			return redirect()->back()
			->withErrors($validator)
			->withInput();
		} else {
			$employee = Employee::find($id);
			if ($request->file('foto_ktp')) {
				$ktp = $request->file('foto_ktp');
				$foto_ktp = time()."_".rand(1,99999).".".$ktp->getClientOriginalExtension();
				$ktp_path = 'uploads/ktp';
				$ktp->move($ktp_path, $foto_ktp);
			} else {
				$foto_ktp = "default.png";
			}
			if ($request->file('foto_tabungan')) {
				$tabungan = $request->file('foto_tabungan');
				$foto_tabungan = time()."_".rand(1,99999).".".$tabungan->getClientOriginalExtension();
				$tabungan_path = 'uploads/tabungan';
				$tabungan->move($tabungan_path, $foto_tabungan);
			} else {
				$foto_ktp = "default.png";
			}
			if ($request->file('foto_profile'))
			{
				$tabungan = $data['foto_profile'];
				$foto_profile = Str::random().time()."_".rand(1,99999).".".$tabungan->getClientOriginalExtension();
				$tabungan_path = 'uploads/profile';
				$tabungan->move($tabungan_path, $foto_profile);
			} else {
				$foto_profile = "default.png";
			}
			if($request->file('foto_ktp')){
				$employee->foto_ktp = $foto_ktp;
			}
			if($request->file('foto_tabungan')){
				$employee->foto_tabungan = $foto_tabungan;
			}
			if ($request->input('status') == 'Stay') {
				$employee->status = $request->input('status');
			}
			if ($request->input('status') == 'Mobile') {
				$employee->status = $request->input('status');
			}
			if ($request->input('position') == Position::where(['level' => 'tlmtc'])->first()->id) {
				$employee->id_subarea = $request->input('subarea');
			}
			if ($request->input('password')) {
				$employee->password = bcrypt($request->input('password'));
			}
			$employee->name 		= $request->input('name');
			$employee->nik 			= $request->input('nik');
			$employee->ktp 			= $request->input('ktp');
			$employee->phone 		= $request->input('phone');
			$employee->email 		= $request->input('email');
			$employee->rekening 	= $request->input('rekening');
			$employee->bank 		= $request->input('bank');
			$employee->gender 		= $request->input('gender');
			$employee->education 	= $request->input('education');
			$employee->birthdate 	= $request->input('birthdate');
			$employee->id_position 	= $request->input('position');
			$employee->id_agency 	= $request->input('agency');
			$employee->save();

			/*
			*	Process Update
			*/
			if ($request->input('status') == 'Stay') {
				EmployeeStore::where('id_employee', $id)->delete();
				EmployeeStore::create([
					'id_store' 		=> $request->input('store'),
					'id_employee' 	=> $id,
				]);
				return redirect()->route('employee')
				->with([
					'type' 		=> 'success',
					'title' 	=> 'Sukses!<br/>',
					'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah employee!'
				]);
			} else if($request->input('status') == 'Mobile') {
				EmployeeStore::where('id_employee', $id)->delete();
				$dataStore = array();
				foreach ($request->input('stores') as $store) {
					$dataStore[] = array(
						'id_employee' 	=> $id,
						'id_store' 		=> $store,
					);
				}
				DB::table('employee_stores')->insert($dataStore);
				return redirect()->route('employee')
				->with([
					'type' 		=> 'success',
					'title' 	=> 'Sukses!<br/>',
					'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah employee!'
				]);
			} else if (!empty($request->input('pasar'))) {
				EmployeePasar::where('id_employee', $id)->delete();
				$dataPasar = array();
				foreach ($request->input('pasar') as $pasar) {
					$dataPasar[] = array(
						'id_employee' 	=> $id,
						'id_pasar' 		=> $pasar,
					);
				}
				DB::table('employee_pasars')->insert($dataPasar);
				return redirect()->route('employee.pasar')
				->with([
					'type' 		=> 'success',
					'title' 	=> 'Sukses!<br/>',
					'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah employee!'
				]);
			} else if (!empty($request->input('subarea'))) {
				EmployeeSubArea::where('id_employee', $id)->delete();
				$dataSubArea = array();
				foreach ($request->input('subarea') as $subarea) {
					$dataSubArea[] = array(
						'id_employee' 	=> $id,
						'id_subarea' 	=> $subarea,
					);
				}
				DB::table('employee_sub_areas')->insert($dataSubArea);
				return redirect()->route('employee.pasar')
				->with([
					'type' 		=> 'success',
					'title' 	=> 'Sukses!<br/>',
					'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah employee!'
				]);
			}
		}
	}

	public function delete($id)
	{
		$employee = Employee::find($id);
		$employee->delete();
		return redirect()->back()
		->with([
			'type'    => 'success',
			'title'   => 'Sukses!<br/>',
			'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
		]);
	}

	public function export()
	{
		$emp = Employee::where(['isResign' => false])
		->whereIn('id_position', [1,2,6])
		->orderBy('created_at', 'DESC')
		->get();
		$dataBrand = array();
		foreach ($emp as $val) {
			$store = EmployeeStore::where(
				'id_employee', $val->id
			)->get();
			$storeList = array();
			foreach($store as $dataStore) {
				if(isset($dataStore->id_store)) {
					$storeList[] = $dataStore->store->name1;
				} else {
					$storeList[] = "-";
				}
			}
			$data[] = array(
				'NIK'          	=> $val->nik,
				'Name'          => $val->name,
				'KTP'         	=> $val->ktp,
				'Phone'         => $val->phone,
				'Email'     	=> $val->email,
				'Timezone'		=> $val->timezone->name,
				'Rekening'      => (isset($val->rekening) ? $val->rekening : "-"),
				'Bank' 		    => (isset($val->bank) ? $val->bank : "-"),
				'Join Date'		=> $val->joinAt,
				'Agency'		=> $val->agency->name,
				'Gender'		=> $val->education,
				'Birthdate'		=> $val->birthdate,
				'Position'		=> $val->position->name,
				'Status'		=> (isset($val->status) ? $val->status : "-"),
				'Store'			=> rtrim(implode(',', $storeList), ',') ? rtrim(implode(',', $storeList), ',') : "-"
			);
		}
		$filename = "employee_".Carbon::now().".xlsx";
		return Excel::create($filename, function($excel) use ($data) {
			$excel->sheet('Employee', function($sheet) use ($data)
			{
				$sheet->fromArray($data);
			});
		})->download();
	}
}
