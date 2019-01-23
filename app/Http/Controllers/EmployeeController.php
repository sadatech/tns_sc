<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use DB;
use Auth;
use File;
use Excel;
use App\PlanEmployee;
use App\TargetGtc;
use Carbon\Carbon;
use App\Position;
use App\Agency;
use App\SubArea;
use App\Area;
use App\Region;
use App\Account;
use App\Channel;
use App\SalesTiers;
use App\Store;
use App\Timezone;
use App\Employee;
use App\EmployeePasar;
use App\EmployeeSubArea;
use App\Pasar;
use App\Block;
use App\EmployeeStore;
use App\Filters\EmployeeFilters;
use App\Filters\BlockFilters;
use App\Traits\StringTrait;
use App\Traits\FirstOrCreateTrait;

class EmployeeController extends Controller
{
	use StringTrait, FirstOrCreateTrait;

	public function getDataWithFilters(EmployeeFilters $filters){
		$data = Employee::filter($filters)->where('isResign', 0)->get();
		return $data;
	}

	public function getDataWithFiltersBlock(BlockFilters $filters){
		$data = Block::filter($filters)->get();
		return $data;
	}

	public function getDataWithFiltersForReport(EmployeeFilters $filters){
		$data = Employee::filter($filters)->get();
		return $data;
	}

	public function getDataIsTL()
	{
		$data = Employee::where("id_position", 5)
		->whereHas("employeeSubArea", function($query){
			$query->where("isTl", 1);
		})
		->get();
		return $data;
	}

	public function baca()
	{
		return view('employee.employee');
	}

	public function read($param = '')
	{
		$data['timezone'] 	= Timezone::all();
		if(Auth::user()->role->level == 'AdminGtc'){
			$data['position'] 	= Position::whereIn('level', ['spggtc', 'mdgtc', 'dc', 'tlgtc', 'motoric'])->get();	
		}else if(Auth::user()->role->level == 'AdminMtc'){
			$data['position'] 	= Position::whereIn('level', ['spgmtc', 'mdmtc', 'tlmtc'])->get();
		}else{
			$data['position'] 	= Position::get();
		}
		if($param != null){
			if($param == 'dc'){
				$data['position'] = Position::where('level', 'dc')->get();
			}
		}
		$data['agency'] 	= Agency::get();
		$data['store'] 		= Store::get();
		$data['pasar'] 		= Pasar::get();
		$data['subarea'] 	= SubArea::get();
		return view('employee.employeecreate', $data);
	}
	public function readupdate($id, $param = '')
	{
		$data['timezone'] 	= Timezone::all();
		$data['emp'] 		= Employee::where(['id' => $id])->first();
		if(Auth::user()->role->level == 'AdminGtc'){
			$data['position'] 	= Position::whereIn('level', ['spggtc', 'mdgtc', 'dc', 'tlgtc', 'motoric'])->get();	
		}else if(Auth::user()->role->level == 'AdminMtc'){
			$data['position'] 	= Position::whereIn('level', ['spgmtc', 'mdmtc', 'tlmtc'])->get();
		}else{
			$data['position'] 	= Position::get();
		}
		if($param != null){
			if($param == 'dc'){
				$data['position'] = Position::where('level', 'dc')->get();
			}
		}
		$data['agency'] 	= Agency::get();
		$data['store'] 		= Store::get();
		$data['pasar'] 		= Pasar::get();
		$data['subarea'] 	= SubArea::get();
		$data['store_selected'] = json_encode(EmployeeStore::where(['employee_stores.id_employee' => $id])->join('stores','stores.id','employee_stores.id_store')->select(DB::raw("concat(stores.id,'|',stores.name1) as stores_item"))->get()->toArray());
		$data['pasar_selected'] = json_encode(EmployeePasar::where(['employee_pasars.id_employee' => $id])->join('pasars','pasars.id','employee_pasars.id_pasar')->select(DB::raw("concat(pasars.id,'|',pasars.name) as pasars_item"))->get()->toArray());
		$data['area_selected'] = json_encode(EmployeeSubArea::where(['employee_sub_areas.id_employee' => $id])->join('sub_areas','sub_areas.id','employee_sub_areas.id_subarea')->select(DB::raw("concat(sub_areas.id,'|',sub_areas.name,'|') as subarea_item"))->get()->toArray());
		$data['isTl'] = (isset(EmployeeSubArea::where('id_employee', $id)->first()->isTl) ? EmployeeSubArea::where('id_employee', $id)->first()->isTl : 0);
		// dd($data);
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
			'foto_ktp' 		=> 'max:10000',
			'foto_tabungan' => 'max:10000|mimes:jpeg,jpg,bmp,png',
			'name' 			=> 'required',
			'password' 		=> 'required',
			'position' 		=> 'required',
			'agency' 		=> 'required|numeric',
			'email' 		=> 'email|required|unique:employees',
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
			return response()->json([
				'type' 		=> 'danger',
				'title' 	=> 'Error!<br/>',
				'message'	=> implode("<br>", $validator->messages()->all())
			]);
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
					'foto_profile' 	=> $foto_profile,
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
						return response()->json([
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
						return response()->json([
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
						return response()->json([
							'type' 		=> 'success',
							'title' 	=> 'Sukses!<br/>',
							'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
						]);
					} else if (!empty($request->input('subarea'))) {
						$dataSubArea = array();
						foreach ($request->input('subarea') as $subarea) {
							$dcCheck = Position::where('level', 'dc')->first();
							if ($request->input('position') == $dcCheck->id)
							{
								if ($request->input('tl'))
								{
									$isTl = true;
								} else {
									$isTl = false;
								}
							} else {
								$isTl = true;
							}
							$dataSubArea[] = array(
								'id_employee' 	=> $insert->id,
								'isTl'			=> $isTl,
								'id_subarea' 	=> $subarea
							);
						}
						DB::table('employee_sub_areas')->insert($dataSubArea);
						return response()->json([
							'type' 		=> 'success',
							'title' 	=> 'Sukses!<br/>',
							'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
						]);
					}
				}
			} else {
				return response()->json([
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
			// return '';
			if (isset($employee->id)) {
			$employeeS 		= EmployeeStore::where(['id_employee' => $employee->id])->get();
				$store 		= array();
				$address 	= array();
				$coverage 	= array();
				$acc		= array();
				foreach ($employeeS as $key => $data) 
				{
					$getOutlet = Store::where(['id' => $data->id_store]);
					foreach ($getOutlet->get() as $val) {
						$acc[$data->id][] 		= $val->account->name;
						$address[$data->id][] 	= $val->address;
						$coverage[$data->id][] 	= $val->coverage;
					}
					if ($getOutlet->count() < 1) {
						$store[] = (isset($data->store->name1) ? "<tr><td>".$data->store->name1."</td><td>Kosong</td></tr>" : "");
					} else {
						$store[] = (isset($data->store->name1) ? "<tr><td>".$data->store->name1."</td><td>".rtrim(implode(', ', $acc[$data->id]), ',')."</td><td>".rtrim(implode(', ', $address[$data->id]), ',')."</td><td>".rtrim(implode(', ', $coverage[$data->id]), ',')."</td></tr>" : "");
					}
				}

			$data = array(
				'id'        	=> (isset($employee->id) ? $employee->id : ""),
				'store'    		=> $store
			);
			return "<a href=".route('ubah.employee', $employee->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
			<button data-url=".route('employee.delete', $employee->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
			<button onclick='viewModal(".json_encode($data).")' class='btn btn-sm btn-warning btn-square' title='View Store'><i class='si si-picture mr-2'></i> STORE</button>
			<a href=".asset('/uploads/ktp')."/".$employee->foto_ktp." class='btn btn-sm btn-success btn-square popup-image' title='Show Photo KTP'><i class='si si-picture mr-2'></i> KTP</a>
			<a href=".asset('/uploads/tabungan')."/".$employee->foto_tabungan." class='btn btn-sm btn-info btn-square popup-image' title='Show Photo Tabungan'><i class='si si-picture mr-2'></i> TABUNGAN</a>";
			}
		})
		->addColumn('employeeStore', function($employee) {
			// return '';
			$store = EmployeeStore::where(['id_employee' => $employee->id])->get();
			$storeList = array();
			foreach ($store as $data) {
				$storeList[] = $data->store->name1;
			}
			return rtrim(implode(', ', $storeList), ',');
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
				$employee->employeeSubArea->id_subarea = $request->input('subarea');
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
						'isTl'			=> $request->input('tl'),
						'id_subarea' 	=> $subarea
					);
				}
				DB::table('employee_sub_areas')->insert($dataSubArea);
				return redirect()->route('employee.dc')
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
		{
			$emp 		= Employee::find($id);
			$gtc 		= TargetGtc::where(['id_employee' => $emp->id])->count();
			$dc 		= PlanEmployee::where(['id_employee' => $emp->id])->count();
			$jumlah= $gtc + $dc;
			if (!$jumlah < 1) {
				return redirect()->back()
				->with([
					'type'    => 'danger',
					'title'   => 'Gagal!<br/>',
					'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain di TargetSMD, Plan DemoCooking!'
				]);
			} else {
				$emp->delete();
				return redirect()->back()
				->with([
					'type'      => 'success',
					'title'     => 'Sukses!<br/>',
					'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
			   ]);
			}
		}
	}

	public function export()
	{
		$emp = Employee::where(['isResign' => false])
		->whereIn('id_position', [1,2,6])
		->orderBy('created_at', 'DESC');
		if ($emp->count() > 0) {
			$dataBrand = array();
			foreach ($emp->get() as $val) {
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
					'KTP'         	=> (isset($val->ktp) ? $val->ktp : "-"),
					'Phone'         => (isset($val->phone) ? $val->phone : "-"),
					'Email'     	=> (isset($val->email) ? $val->email : "-"),
					'Timezone'		=> $val->timezone->name,
					'Rekening'      => (isset($val->rekening) ? $val->rekening : "-"),
					'Bank' 		    => (isset($val->bank) ? $val->bank : "-"),
					'Join Date'		=> (isset($val->joinAt) ? $val->joinAt : ""),
					'Agency'		=> $val->agency->name,
					'Gender'		=> $val->gender,
					'Education'		=> (isset($val->education) ? $val->education : ""),
					'Birthdate'		=> (isset($val->birthdate) ? $val->birthdate : ""),
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
		} else {
			return redirect()->back()
			->with([
				'type'   => 'danger',
				'title'  => 'Gagal Unduh!<br/>',
				'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
			]);
		}
	}

	public function import(Request $request)
    {
        $this->validate($request, [
            'file'      => 'required'
        ]);
        $transaction = DB::transaction(function () use ($request) {
            $file = Input::file('file')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($extension != 'xlsx' && $extension !=  'xls') {
                return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
            }
            if($request->hasFile('file')){
                $file = $request->file('file')->getRealPath();
                $ext = '';
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use ($request)
                {
                    foreach($results as $row)
                    {
						$rowRules = [
							'nik' 		=> 'required|numeric',
							'name'		=> 'required',	
							'password'	=> 'required',
							'agency'	=> 'required'
						];
                        $validator = Validator($row->toArray(), $rowRules);
                        if ($validator->fails()) {
                            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
                        } else {
							// $check = Employee::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->name))."'")
							// ->where(['nik' => $row->nik, 'isResign' => false])
							// ->whereIn('id_position', [1,2,6])
							// ->count();

							// if ($check < 1) {
								$id_agency = $this->findAgen($row['agency']);
								
								$getPosisi 	= Position::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->position))."'")->first()->id;
								// $getPosition = Position::where('level', $row->position)->first()->id;

								$getTimezone = Timezone::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row['timezone']))."'")->first()->id;
                            	$insert = Employee::updateOrCreate(
                            		[
										'nik'              	=> $this->removeFirstQuotes($row['nik']),
										'ktp'				=> (isset($row->ktp) ? $this->removeFirstQuotes($row->ktp) : ""),
									],[
										'phone'   			=> (isset($row['phone']) ? $this->removeFirstQuotes($row['phone']) : ""),
										'email'   			=> (isset($row['email']) ? $row['email'] : ""),
										'name'				=> $row['name'],   
										'id_timezone'		=> ($getTimezone ? $getTimezone : 1),
										'rekening'			=> (isset($row->rekening) ? $this->removeFirstQuotes($row->rekening) : ""),
										'bank' 				=> (isset($row->bank) ? $row->bank : ""),
										'joinAt'			=> (isset($row->join_date) ? Carbon::parse($this->removeFirstQuotes($row->join_date)) : ""),
										'id_agency'			=> $id_agency,
										'gender'			=> ($row->gender ? $row->gender : "Perempuan"),
										'education'			=> ($row->education ? $row->education : "SLTA"),
										'birthdate'			=> (isset($row->birth_date) ? Carbon::parse($this->removeFirstQuotes($row->birth_date)) : ""),
										'password'			=> bcrypt($row['password']),
										'id_position'		=> ($getPosisi ? $getPosisi : 1),
										'status'			=> (isset($row->status) ? $row->status : ""),
	                            		'foto_ktp' 			=> "default.png",
										'foto_tabungan'		=> "default.png",
	                            	]
	                            );                            	
								
								$employee_id = $insert->id;

                            		if ($insert->status != "")  {
                            			if(!($row->store == '' || $row->store == null)){
											$dataStore = array();
											$listStore = explode(",", $row->store);
											foreach ($listStore as $store) {
												EmployeeStore::firstOrCreate(
													[
														'id_store'		=> $this->findStore(
															$store, $row->subarea, $row->area, $row->region, $row->account, $row->channel,
															$row->timezone_store, $row->sales_tier
														),
														'id_employee'	=> $employee_id
													]
												);
											}
											
										}
									}
								// }return false;
                            };
                        }
                },false);
            }
            return 'success';
        });

        if ($transaction == 'success') {
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil import!'
            ]);
        } else {
            return redirect()->back()
            ->with([
                'type'    => 'danger',
                'title'   => 'Gagal!<br/>',
                'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
            ]);
        }
	}
	
}
