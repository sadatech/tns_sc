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
use App\Outlet;
use Carbon\Carbon;
use App\Position;
use App\Agency;
use App\SubArea;
use App\Brand;
use App\Store;
use App\Timezone;
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
				$eP 		= EmployeePasar::where(['id_employee' => ($employee->id)])->first();
				$employeeS 	= EmployeePasar::where(['id_employee' => $employee->id])->get();
				$eOut 		= Outlet::where(['id_employee_pasar' => $eP->id])->get();
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
			foreach($pasar as $dataPasar) {
				if(isset($dataPasar->id_pasar)) {
					$pasarList[] = $dataPasar->pasar->name;
				} else {
					$pasarList[] = "-";
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
				'Pasar'			=> rtrim(implode(',', $pasarList), ',') ? rtrim(implode(',', $pasarList), ',') : "-"
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
        $id_company = Auth::user()->id_company;
        // $this->validate($request, [
        //     'file' =>   'required|mimeTypes:'.
        //                 'application/vnd.ms-office,'.
        //                 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'.
        //                 'application/vnd.ms-excel'
        // ]);

        $transaction = DB::transaction(function () use ($request, $id_company) {
            $file = Input::file('file')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($extension != 'xlsx' && $extension !=  'xls') {
                return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
            }
            if($request->hasFile('file')){
                $file = $request->file('file')->getRealPath();
                $ext = '';
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use ($id_company)
                {
                    foreach($results as $row)
                    {
                        echo "$row<hr>";

                        $dataAgency['agency_name']   = $row->agency;
                        $id_agency = $this->findAgen($dataAgency);
						
                        $insert = Employee::create([
                            'photo'             => "default.png",
                            'name1'             => $row->name,
                            'name2'             => (isset($row->optionalname) ? $row->optionalname : "-"),
                            'type'              => $row->type,
                            'id_subarea'        => $id_subarea,
                            'id_account'        => $id_account,
                            'id_classification' => $id_classification,
                            'longitude'         => $row->longitude,
                            'latitude'          => $row->latitude,
                            'address'           => $row->address,
                            'store_phone'       => (isset($row->store_phone) ? $row->store_phone : "-"),
                            'owner_phone'       => (isset($row->owner_phone) ? $row->owner_phone : "-")
                        ]);
                        if ($insert) {
                            $dataStore = array();
                            $listDist = explode(",", $row->distributor);
                            foreach ($listDist as $dist) {
                                $dataStore[] = array(
                                    'id_distributor'    => $this->findDistributor($dist),
                                    'id_store'          => $insert->id,
                                );
                            }
                            DB::table('store_distributors')->insert($dataStore);
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
}
