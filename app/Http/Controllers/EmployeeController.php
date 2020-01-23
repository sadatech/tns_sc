<?php

namespace App\Http\Controllers;

use App\Account;
use App\Agency;
use App\Area;
use App\Block;
use App\Channel;
use App\Employee;
use App\EmployeePasar;
use App\EmployeeRoute;
use App\EmployeeStore;
use App\EmployeeSubArea;
use App\Filters\BlockFilters;
use App\Filters\EmployeeFilters;
use App\Pasar;
use App\PlanEmployee;
use App\Position;
use App\Region;
use App\Route;
use App\SalesTiers;
use App\Store;
use App\SubArea;
use App\TargetGtc;
use App\Timezone;
use App\Traits\FirstOrCreateTrait;
use App\Traits\StringTrait;
use Auth;
use Carbon\Carbon;
use DB;
use Excel;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;

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

	public function getDataIsTL(EmployeeFilters $filters)
	{
		$data = Employee::filter($filters)->where("id_position", 5)
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
		$data['pasar_selected'] = json_encode(EmployeePasar::where(['employee_pasars.id_employee' => $id, 'active'=>'1'])->join('pasars','pasars.id','employee_pasars.id_pasar')->select(DB::raw("concat(pasars.id,'|',pasars.name) as pasars_item"))->get()->toArray());
		$data['area_selected'] = json_encode(EmployeeSubArea::where(['employee_sub_areas.id_employee' => $id])->join('sub_areas','sub_areas.id','employee_sub_areas.id_subarea')->select(DB::raw("concat(sub_areas.id,'|',sub_areas.name,'|') as subarea_item"))->get()->toArray());
		$data['isTl'] = (isset(EmployeeSubArea::where('id_employee', $id)->first()->isTl) ? EmployeeSubArea::where('id_employee', $id)->first()->isTl : 0);

		$data['pasar_selected'] = json_encode(EmployeeRoute::where(['employee_routes.id_employee' => $id, 'type'=>'2'])->join('routes','routes.id','employee_routes.id_route')->select(DB::raw("concat(routes.id,'|',routes.name) as pasars_item"))->get()->toArray());
		$data['route_selected'] = json_encode(EmployeeRoute::where(['employee_routes.id_employee' => $id, 'type'=>'1'])->join('routes','routes.id','employee_routes.id_route')->select(DB::raw("concat(routes.id,'|',routes.name) as routes_item"))->get()->toArray());

		// dd($data);
		if ($data['emp']->isResign) {
			return redirect()->route('employee');
		} else {
			return view('employee.employeeupdate', $data);
		}
	}				

	public function store(Request $request)
	{
		$request['position'] = 1;
		$data=$request->all();
	
		$limit=[
			'foto_ktp' 		=> 'max:10000',
			'foto_tabungan' => 'max:10000|mimes:jpeg,jpg,bmp,png',
			'name' 			=> 'required',
			'password' 		=> 'required',
			'position' 		=> 'required',
			'agency' 		=> 'numeric',
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
					'id_timezone' 	=> @$request->input('timezone'),
					'id_agency' 	=> @$request->input('agency')
				]);
				if ($insert->id) {
					if (!empty($request->input('pasar'))) {
						$dataPasar = array();
						foreach ($request->input('pasar') as $pasar) {
							$dataPasar[] = array(
								'id_employee' 	=> $insert->id,
								'id_route' 		=> $pasar,
								'created_at'	=> Carbon::now(),
								'updated_at'	=> Carbon::now(),
							);
						}
						DB::table('employee_routes')->insert($dataPasar);						
					}

					if (!empty($request->input('route'))) {
						$dataRoute = array();
						foreach ($request->input('route') as $route) {
							$dataRoute[] = array(
								'id_employee' 	=> $insert->id,
								'id_route' 		=> $route,
								'created_at'	=> Carbon::now(),
								'updated_at'	=> Carbon::now(),
							);
						}
						DB::table('employee_routes')->insert($dataRoute);						
					}

					return response()->json([
						'type' 		=> 'success',
						'title' 	=> 'Sukses!<br/>',
						'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah employee!'
					]);
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
		$employee = Employee::where(['isResign' => 0])
		->with(['timezone'])
		->select('employees.*');
		return Datatables::of($employee)
		->addColumn('coverage', function ($employee) {
			$employeeS 		= EmployeeRoute::where(['id_employee' => $employee->id])->get();
			$name 		= array();
			$type 		= array();
			$area 		= array();
			$address	= array();
			$just_name  = array();
			foreach ($employeeS as $key => $data)
			{
				$getOutlet = Route::where(['id' => $data->id_route])->get();
				foreach ($getOutlet as $val) {
					$name[$data->id][] 		= str_replace("'", "`", $val->name);
					$type[$data->id][] 		= str_replace("'", "`", (($val->type == 1) ? "Route" : "Market"));
					$area[$data->id][] 		= str_replace("'", "`", $val->subarea->name.", ".$val->subarea->area->name);
					$address[$data->id][] 	= str_replace("'", "`", $val->address);
					array_push($just_name, str_replace("'", "`", $val->name));
				}
				if ($getOutlet->count() < 1) {
					$store[] = "<tr><td colspan=4>Kosong</td></tr>";
				} else {
					$store[] = "<tr><td>".rtrim(implode(', ', $name[$data->id]), ',')."</td><td>".rtrim(implode(', ', $type[$data->id]), ',')."</td><td>".rtrim(implode(', ', $area[$data->id]), ',')."</td><td>".rtrim(implode(', ', $address[$data->id]), ',')."</td></tr>";
				}
			}

			$data = array(
				'id'        	=> (isset($employee->id) ? $employee->id : ""),
				'store'    		=> $store
			);

			// $temp_name = implode(', ', array_values($name));

			$naming = implode(', ', $just_name);
			$naming = (strlen($naming) > 40) ? substr($naming, 0, 40) . ' ...' : $naming;


			return " <span class='fake-link' onclick='viewModal(".json_encode($data).")'>".$naming."</span>";
		})
		->addColumn('action', function ($employee) {

			// ADD PHOTO PATH
			$employee['foto_ktp_path'] = ($employee['foto_ktp'] != null) ? asset('uploads/ktp/'.$employee['foto_ktp']) : asset('no-image.jpg');
			$employee['foto_profile_path'] = ($employee['foto_profil_url'] != null) ? asset('uploads/profile/'.$employee['foto_profil_url']) : asset('no-image.jpg');
			$employee['foto_tabungan_path'] = ($employee['foto_tabungan'] != null) ? asset('uploads/tabungan/'.$employee['foto_tabungan']) : asset('no-image.jpg');
			
			return "<button onclick='viewInfo(".json_encode($employee).")' class='btn btn-sm btn-success btn-square js-tooltip-enabled' data-toggle='tooltip' data-placement='top' title='Employee Detail'><i class='si si-info'></i></button>
					<a href=".route('ubah.employee', $employee->id)."/mtc"." class='btn btn-sm btn-primary btn-square js-tooltip-enabled' data-toggle='tooltip' data-placement='top' title='Update'><i class='si si-pencil'></i></a>
					<button data-url=".route('employee.delete', $employee->id)." class='btn btn-sm btn-danger btn-square js-swal-delete js-tooltip-enabled' data-toggle='tooltip' data-placement='top' title='Delete'><i class='si si-trash'></i></button>";
		})
		->rawColumns(['action', 'coverage'])
		->make(true);
	}

	public function update(Request $request, $id) 
	{
		$request['position'] = 1;
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
			'agency'				=> 	'numeric',
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
			$employee->save();

			/*
			*	Process Update
			*/
			$statusParam = [
					'type' 		=> 'success',
					'title' 	=> 'Sukses!<br/>',
					'message'	=> '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah employee!'
				];

			EmployeeRoute::where('id_employee', $id)->delete();

			if (!empty($request->input('pasar'))) {				
				$dataPasar = array();
				foreach ($request->input('pasar') as $pasar) {
					$dataPasar[] = array(
						'id_employee' 	=> $id,
						'id_route' 		=> $pasar,
						'created_at'	=> Carbon::now(),
						'updated_at'	=> Carbon::now(),
					);
				}
				DB::table('employee_routes')->insert($dataPasar);						
			}

			if (!empty($request->input('route'))) {
				$dataRoute = array();
				foreach ($request->input('route') as $route) {
					$dataRoute[] = array(
						'id_employee' 	=> $id,
						'id_route' 		=> $route,
						'created_at'	=> Carbon::now(),
						'updated_at'	=> Carbon::now(),
					);
				}
				DB::table('employee_routes')->insert($dataRoute);						
			}

			return redirect()->route('employee')->with($statusParam);			
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
		$emp = Employee::where(['isResign' => 0])
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
							// ->where(['nik' => $row->nik, 'isResign' => 0])
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
											$dataStore['name'] 	= $row->store ?? '';
											$dataStore['name2'] = $row->optional_name ?? '';
											$dataStore['code'] 	= $row->code ?? '';
											$dataStore['address'] 		= $row->address ?? '';
											$dataStore['delivery'] 		= (strtoupper($row->delivery) == 'DIRECT')? 'Direct' : 'DC';
											$dataStore['is_jawa'] 		= ($row->is_jawa == 'Y')?'Jawa' : 'Non Jawa';
											$dataStore['is_vito'] 		= ($row->is_vito == 'Y')?'Vito' : 'Non Vito';
											$dataStore['coverage'] 		= (strtoupper($row->coverage) == 'DIRECT')? 'Direct' : 'In Direct';
											$dataStore['store_panel'] 	= ($row->store_panel == 'Y')?'YES' : 'NO';
											EmployeeStore::firstOrCreate(
												[
													'id_store'		=> $this->findStore(
														$dataStore, $row->subarea, $row->area, $row->region, $row->account, $row->channel,
														$row->timezone_store, $row->sales_tier
													),
													'id_employee'	=> $employee_id
												]
											);
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
