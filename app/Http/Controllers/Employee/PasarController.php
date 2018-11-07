<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use File;
use Excel;
use App\Area;
use App\Region;
use App\Outlet;
use App\Pasar;
use Carbon\Carbon;
use App\Agency;
use App\SubArea;
use App\Employee;
use App\EmployeePasar;
use App\Filters\EmployeeFilters;

class PasarController extends Controller
{
	public function getDataWithFilters(EmployeeFilters $filters){
		$data = Employee::filter($filters)->get();
		return $data;
	}

	public function baca()
	{
		return view('employee.pasar');
	}

	public function data()
	{
		$employee = Employee::where(['isResign' => false])
		->whereIn('id_position', [3,4])
		->with(['agency', 'position', 'employeePasar', 'timezone'])
		->select('employees.*');
		return Datatables::of($employee)
		->addColumn('employeePasar', function($employee) {
			$pasar = EmployeePasar::where(['id_employee' => $employee->id])->get();
			$pasarList = array();
			foreach ($pasar as $data) {
				$pasarList[] = $data->pasar->name;
			}
			return rtrim(implode(', ', $pasarList), ',');
		})
		->addColumn('action', function ($employee) {
			if (isset($employee->id)) {
				$employeeS = EmployeePasar::where(['id_employee' => $employee->id])->get();
				$pasar = array();
				$outlist = array();
				foreach ($employeeS as $key => $data) 
				{
					$getOutlet = Outlet::where(['id_employee_pasar' => $data->id]);
					foreach ($getOutlet->get() as $val) {
						$outlist[$data->id][] = $val->name. " (".$val->phone.")";
					}
					if ($getOutlet->count() < 1) {
						$pasar[] = (isset($data->pasar->name) ? "<tr><td>".$data->pasar->name."</td><td>Kosong</td></tr>" : "");
					} else {
						$pasar[] = (isset($data->pasar->name) ? "<tr><td>".$data->pasar->name."</td><td>".rtrim(implode(', ', $outlist[$data->id]), ',')."</td></tr>" : "");
					}
				}
				$data = array(
					'id'        	=> (isset($employee->id) ? $employee->id : ""),
					'pasar'    		=> $pasar,
				);
				return "<a href=".route('ubah.employee', $employee->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
				<button data-url=".route('employee.delete', $employee->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
				<button onclick='viewModal(".json_encode($data).")' class='btn btn-sm btn-warning btn-square' title='View Store'><i class='si si-picture mr-2'></i> OUTLET</button>
				<a href=".asset('/uploads/ktp')."/".$employee->foto_ktp." class='btn btn-sm btn-success btn-square popup-image' title='Show Photo KTP'><i class='si si-picture mr-2'></i> KTP</a>
				<a href=".asset('/uploads/tabungan')."/".$employee->foto_tabungan." class='btn btn-sm btn-info btn-square popup-image' title='Show Photo Tabungan'><i class='si si-picture mr-2'></i> TABUNGAN</a>";
			}
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

	public function export()
	{
		$emp = Employee::where(['isResign' => false])
		->whereIn('id_position', [3,4])
		->orderBy('created_at', 'DESC')
		->get();
		$dataBrand = array();
		foreach ($emp as $val) {
			$pasar = EmployeePasar::where(
				'id_employee', $val->id
			)->get();
			$pasarList = array();
			$outletList = array();
			foreach($pasar as $dataPasar) {
				if(isset($dataPasar->id_pasar)) {
					// $getOutlet = Outlet::where(['id_employee_pasar' => $dataPasar->id])->get();
					// foreach ($getOutlet as $value) {
					// 	$outletList[] = $value->name;
					// }
					$pasarList[] = $dataPasar->pasar->name;
				} else {
					$pasarList[] = "-";
				}
			}
			// dd($outletList);
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
				'Pasar'			=> (isset($pasarList) ? rtrim(implode(',', $pasarList), ',') : "-"),
				'Outlet'		=> (isset($outletList) ? rtrim(implode(',', $outletList), ',') : "-"),
			);
		}
		$filename = "employeePasar_".Carbon::now().".xlsx";
		return Excel::create($filename, function($excel) use ($data) {
			$excel->sheet('EmployeePasar', function($sheet) use ($data)
			{
				$sheet->fromArray($data);
			});
		})->download();
	}

	public function import(Request $request)
    {
        $this->validate($request, [
            'file' =>   'required'
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
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results)
                {
                    foreach($results as $row)
                    {
						echo "$row<hr>";
						$dataSub['subarea_name']   	= $row->subarea;
						$dataSub['area_name']   	= $row->area;
						$dataSub['region_name']   	= $row->region;
						$id_subarea = $this->findSub($dataSub);

                        $dataAgency['agency_name']   = $row->agency;
                        $id_agency = $this->findAgen($dataAgency);

                        $insert = Employee::create([
                            'foto_ktp' 			=> "default.png",
							'foto_tabungan'		=> "default.png",
							'foto_profile' 		=> "default.png",
                            'name'             	=> $row->name,
							'nik'              	=> $row->nik,
							'ktp'				=> (isset($row->ktp) ? $row->ktp : "-"),
							'phone'				=> (isset($row->phone) ? $row->phone : "-"),
							'email'				=> (isset($row->email) ? $row->email : "-"),
							'rekening'			=> (isset($row->rekening) ? $row->rekening : "-"),
							'bank'				=> (isset($row->bank) ? $row->rekening: "-"),
							'birthdate'			=> Carbon::now(),
							'id_agency'			=> $id_agency,
                            'id_position'       => 4,
                            'joinAt'            => Carbon::now(),
                            'gender'            => $row->gender,
                            'education'         => $row->education,
                            'password'          => bcrypt($row->password),
                            'id_timezone'       => 1
                        ]);
                        if ($insert) {
                            $dataPasar = array();
                            $listPasar = explode(",", $row->pasar);
                            foreach ($listPasar as $market) {
                                $dataPasar[] = array(
                                    'id_pasar'    			=> $this->findPasar($market),
                                    'id_employee'          	=> $insert->id,
                                );
                            }
                            DB::table('employee_pasars')->insert($dataPasar);
                        }
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
        }else{
            return redirect()->back()
            ->with([
                'type'    => 'danger',
                'title'   => 'Gagal!<br/>',
                'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
            ]);
        }
	}

	public function findPasar($data)
    {
        $dataPasar = Pasar::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data))."'");
        if ($dataPasar->count() == 0) {
            $pasar = Pasar::create([
                'name'       	=> $data,
				'address'       => "-",
				'latitude'		=> "",
				'longitude'		=> "",
				'id_subarea'	=> $id_subarea

            ]);
            if ($pasar) {
                $id_pasar = $pasar->id;
            }
        } else {
            $id_pasar = $dataPasar->first()->id;
        }
        return $id_pasar;
    }

	
	public function findAgen($data)
    {
        $dataAgency = Agency::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['agency_name']))."'")->get();
        if ($dataAgency != null) {
            $agency = Agency::create([
              'name'        => $data['agency_name']
          ]);
            $id_agency = $agency->id;
        } else {
            $id_agency = $dataAgency->first()->id;
        }
        return $id_agency;
	}
	
	public function findSub($data)
    {
        $dataSub = SubArea::where('name','like','%'.trim($data['subarea_name']).'%')->get();
        if ($dataSub != null) {

			$dataArea = $data;
			$id_area = $this->findArea($dataArea);
            $subarea = SubArea::create([
              'name'        => $data['subarea_name'],
              'id_area'     => $id_area
          ]);
            $id_subarea = $subarea->id;
        }else{
            $id_subarea = $dataSub->first()->id;
        }
        return $id_subarea;
    }

    public function findArea($data)
    {
        $dataArea = Area::where('name','like','%'.trim($data['area_name']).'%');
        if ($dataArea->count() == 0) {

			$dataRegion = $data;
			$id_region = $this->findRegion($dataRegion);
            $area = Area::create([
              'name'        => $data['area_name'],
              'id_region'   => $id_region,
          ]);
            $id_area = $area->id;
        }else{
            $id_area = $dataArea->first()->id;
        }
        return $id_area;
    }

    public function findRegion($data)
    {
        $dataRegion = Region::where('name','like','%'.trim($data['region_name']).'%');
        if ($dataRegion->count() == 0) {

            $region = Region::create([
              'name'        => $data['region_name'],
          ]);
            $id_region = $region->id;
        }else{
            $id_region = $dataRegion->first()->id;
        }
        return $id_region;
    }
}
