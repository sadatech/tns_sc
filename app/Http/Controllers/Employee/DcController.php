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
use Carbon\Carbon;
use App\Agency;
use App\SubArea;
use App\Employee;
use App\Area;
use App\Region;
use App\Timezone;
use App\EmployeeSubArea;
use App\Filters\EmployeeFilters;
use App\Traits\StringTrait;
use App\Traits\FirstOrCreateTrait;

class DcController extends Controller
{
    use StringTrait, FirstOrCreateTrait;

	public function getDataWithFilters(EmployeeFilters $filters){
        $data = Employee::filter($filters)->get();
        return $data;
    }

	public function baca()
	{
		return view('employee.dc');
	}

	public function data()
	{
		$employee = Employee::where(['isResign' => false, 'id_position' => 5])
		->with(['agency', 'position', 'employeeSubArea', 'timezone'])
		->select('employees.*');
		// dd($employee->get()[0]);
		return Datatables::of($employee)
		->addColumn('action', function ($employee) {
			// if ($employee->isResign == false) {
				return "<a href=".route('ubah.employee', $employee->id)."/dc"." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
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
		->addColumn('employeeSubArea', function($employee) {
			$subarea = EmployeeSubArea::where(['id_employee' => $employee->id])->get();
			$subareaList = array();
			foreach ($subarea as $data) {
				$subareaList[] = $data->subarea->name;
			}
			return rtrim(implode(', ', $subareaList), ',');
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
        $emp = Employee::where([
		'isResign' => false, 
		'id_position' => 5
        ])->orderBy('created_at', 'DESC');
        if (!empty($emp->count() > 1)) {
		    foreach ($emp->get() as $val) {
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
		    		'SubArea'		=> (isset($val->subarea->name) ? $val->subarea->name : "-"),
		    		'Gender'		=> $val->education,
		    		'Birthdate'		=> $val->birthdate,
		    		'Position'		=> $val->position->name
		    	);
		    }
            $filename = "employeeDemoCooking_".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('Employee', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
           return redirect()->back()
	    	->with([
	    		'type' 		=> 'danger',
	    		'title' 	=> 'Terjadi Kesalahan!<br/>',
	    		'message'	=> '<i class="em em-thinking_face mr-2"></i>Data Excel Kosong!'
	    	]);
        }   
	}

	public function import(Request $request)
    {
        $id_company = Auth::user()->id_company;
        $this->validate($request, [
            'file' =>   'required'
        ]);

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
						$id_agency = $this->findAgen($row->agency);
                        
                        $getZone 		= Timezone::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->timezone))."'")->first()->id;
                        $insert = Employee::updateOrCreate(
                            [
                                'nik'               => $this->removeFirstQuotes($row->nik),
                                'ktp'               => (isset($row->ktp) ? $this->removeFirstQuotes($row->ktp) : "-"),
                            ],[
                                'name'              => $row->name,
                                'phone'             => (isset($row->phone) ? $this->removeFirstQuotes($row->phone) : "-"),
                                'email'             => (isset($row->email) ? $row->email : "-"),
                                'rekening'          => (isset($row->rekening) ? $this->removeFirstQuotes($row->rekening) : "-"),
                                'bank'              => (isset($row->bank) ? $row->bank: "-"),
                                'joinAt'            => (isset($row->join_date) ? Carbon::parse($this->removeFirstQuotes($row->join_date)) : ""),
                                'id_agency'         => $id_agency,
                                'id_position'       => 5,
                                'birthdate'         => (isset($row->birth_date) ? Carbon::parse($this->removeFirstQuotes($row->birth_date)) : ""),
                                'gender'            => ($row->gender ? $row->gender : "Perempuan"),
                                'education'         => ($row->education ? $row->education : "SLTA"),
                                'password'          => bcrypt($row->password),
                                'id_timezone'       => ($getZone ? $getZone : 1),
    							'foto_ktp' 			=> "default.png",
    							'foto_tabungan'		=> "default.png",
    						]
                        );
                        
                        $employee_id = $insert->id;

						if ($insert) {
                            if(!($row->subarea == '' || $row->subarea == null)){
                                $listSub = explode(",", $row->subarea);
                                foreach ($listSub as $sub) {
                                    $data['subarea_name']   = $sub;
                                    $data['area_name']      = $row->area;
                                    $data['region_name']    = $row->region;
                                    EmployeeSubArea::firstOrCreate([
                                        'id_subarea'    	=> $this->findSub($data),
                                        'id_employee'       => $employee_id
                                    ]);
                                }
                            }
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
