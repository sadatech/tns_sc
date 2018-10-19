<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use Carbon\Carbon;
use App\Position;
use App\Agency;
use App\SubArea;
use App\Brand;
use App\Store;
use App\Timezone;
use App\Employee;
use App\EmployeeStore;
use App\EmployeeSpv;
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
		$position 			= Position::where(['level' => 'level 2'])->first();
		$data['spv'] 		= Employee::where(['id_position' => $position->id]);
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
		$position 			= Position::where(['level' => 'level 2'])->first();
		$data['spv'] 		= Employee::where(['id_position' => $position->id])->get();
		$data['store_selected'] = json_encode(EmployeeStore::where(['employee_stores.id_employee' => $id])->join('stores','stores.id','employee_stores.id_store')->select(DB::raw("concat(stores.id,'|',stores.name1) as stores_item"))->get()->toArray());
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
			'position' 		=> 'required|numeric',
			'agency' 		=> 'required|numeric',
			'email' 		=> 'email|required',
			'phone' 		=> 'required|numeric|unique:employees',
			'nik' 			=> 'required',
			'ktp' 			=> 'required|numeric|unique:employees',
			'gender' 		=> 'required',
			'education' 	=> 'required',
			'birthdate' 	=> 'required|date',
			'timezone'		=> 'required',
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

			if ($request->input('status') == null) {
				$status = null;
			} else {
				$status = $request->input('status');
			}

			if ($request->input('position') == Position::where(['level' => 'level 1'])->first()->id) {
				if ($request->input('spv') != null) {
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
						'joinAt' 		=> Carbon::now(),
						'foto_ktp' 		=> $foto_ktp,
						'foto_tabungan' => $foto_tabungan,
						'id_position' 	=> $request->input('position'),
						'id_timezone' 	=> $request->input('timezone'),
                		'id_subarea'	=> $request->input('subarea'),
						'id_agency' 	=> $request->input('agency')
					]);
					if ($insert->id) {
							// $dataBrand = array();
							// 	foreach ($request->input('brand') as $brand) {
							// 		$dataBrand[] = array(
							// 			'id_brand'    			=> $brand,
							// 			'id_employee'          	=> $insert->id
							// 		);
							// 	}
							// 	DB::table('employee_brands')->insert($dataBrand);
						EmployeeSpv::create([
							'id_user' 		=> $request->input('spv'),
							'id_employee' 	=> $insert->id
						]);
						if ($request->input('status') == 'Stay') {
							EmployeeStore::create([
								'id_store' 		=> $request->input('store'),
								'id_employee' 	=> $insert->id,
								'alokasi' 		=> 1
							]);
							return redirect()->route('employee')
							->with([
								'type' 		=> 'success',
								'title' 	=> 'Sukses!<br/>',
								'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
							]);
						} else if($request->input('status') == 'Mobile') {
							$dataStore = array();
							$alokasi = round(1/count($request->input('stores')), 3);
							foreach ($request->input('stores') as $store) {
								$dataStore[] = array(
									'id_employee' 	=> $insert->id,
									'id_store' 		=> $store,
									'alokasi' 		=> $alokasi
								);
							}
							DB::table('employee_stores')->insert($dataStore);
							return redirect()->route('employee')
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
						'message'	=> '<i class="em em-thinking_face mr-2"></i>Kamu belum mengisi supervisor!'
					]);
				}
			} else {
				$insertData = Employee::create([
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
					'joinAt' 		=> Carbon::now(),
					'foto_ktp' 		=> $foto_ktp,
					'foto_tabungan' => $foto_tabungan,
					'id_position' 	=> $request->input('position'),
					'id_timezone' 	=> $request->input('timezone'),
					'id_agency' 	=> $request->input('agency')
				]);
				return redirect()->route('employee')
				->with([
					'type' 		=> 'success',
					'title' 	=> 'Sukses!<br/>',
					'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
				]);
			}
		}
	}  

	public function data()
	{
		$employee = Employee::where(['isResign' => false])->with(['agency', 'subarea', 'position', 'employeeStore', 'timezone'])
		->select('employees.*');
		// dd($employee->get()[0]);
		return Datatables::of($employee)
		->addColumn('action', function ($employee) {
			// if ($employee->isResign == false) {
				return "<a href=".route('ubah.employee', $employee->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
				<button data-url=".route('employee.delete', $employee->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
				<a href=".asset('/uploads/ktp')."/".$employee->foto_ktp." class='btn btn-sm btn-success btn-square popup-image' title='Show Photo KTP'><i class='si si-picture mr-2'></i> KTP</a>
				<a href=".asset('/uploads/tabungan')."/".$employee->foto_tabungan." class='btn btn-sm btn-info btn-square popup-image' title='Show Photo Tabungan'><i class='si si-picture mr-2'></i> TABUNGAN</a>";
			// } else {
			// 	return "<a href=".route('ubah.employee', $employee->id)." class='btn btn-sm btn-primary btn-square disabled' title='Update'><i class='si si-pencil'></i></a>
			// 	<a href=".route('employee.delete', $employee->id)." class='btn btn-sm btn-danger btn-square mr-6 js-swal-delete' title='Delete'><i class='si si-trash'></i></a>
			// 	<a href=".asset('/uploads/ktp')."/".$employee->foto_ktp." class='btn btn-sm btn-success btn-square popup-image' title='Show Photo KTP'><i class='si si-picture mr-2'></i> KTP</a>
			// 	<a href=".asset('/uploads/tabungan')."/".$employee->foto_tabungan." class='btn btn-sm btn-info btn-square popup-image' title='Show Photo Tabungan'><i class='si si-picture mr-2'></i> TABUNGAN</a>";
			// }
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
		->addColumn('subarea', function($employee) {
			if (isset($employee->subarea)) {
				$subarea = $employee->subarea->name;
			} else {
				$subarea = "Without Area";
			}
			return $subarea;
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
			'position'				=> 	'required|numeric',
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
				$foto_ktp = "Excel Dwi Oktavianto orang homo";
			}
			if ($request->file('foto_ktp')) {
				$tabungan = $request->file('foto_tabungan');
				$foto_tabungan = time()."_".rand(1,99999).".".$tabungan->getClientOriginalExtension();
				$tabungan_path = 'uploads/tabungan';
				$tabungan->move($tabungan_path, $foto_tabungan);
			} else {
				$foto_ktp = "Excel Dwi Oktavianto Orang Cabul";
			}
				if($request->file('foto_ktp')){
					$employee->foto_ktp = $foto_ktp;
				}
				if($request->file('foto_tabungan')){
					$employee->foto_tabungan = $foto_tabungan;
				}
				// if ($request->input('brand')) {
    //                 foreach ($request->input('brand') as $brand) {
    //                     EmployeeBrand::where('id_employee', $id)->delete();
    //                     $dataStore[] = array(
    //                         'id_brand'    			=> $brand,
    //                         'id_employee'          	=> $id,
    //                     );
    //                 }
    //                 DB::table('employee_brands')->insert($dataStore);
    //             }
				if ($request->input('status') == 'Stay') {
					$employee->status = $request->input('status');
				}
				if ($request->input('status') == 'Mobile') {
					$employee->status = $request->input('status');
				}
				if ($request->input('position') == Position::where(['level' => 'level 3'])->first()->id) {
					$employee->id_subarea = $request->input('subarea');
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
				// $employee->id_brand 	= $request->input('brand');
				$employee->save();
				return redirect()->route('employee')
				->with([
					'type'    => 'success',
					'title'   => 'Sukses!<br/>',
					'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah employee!'
				]);
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
}
