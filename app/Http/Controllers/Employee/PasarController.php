<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
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
}
