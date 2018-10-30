<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\PlanDc;
use App\PlanEmployee;
use DB;
use Auth;
use File;
use Excel;
use App\Employee;
use Carbon\Carbon;

class PlandcController extends Controller
{
    public function read()
    {
        $data['employee']       = Employee::where('id_position', 5 )->get();
        return view('plandc.plandc', $data);
    }

    public function data()
    {
        $plan = PlanDc::with('planEmployee')
        ->select('plan_dcs.*');
        return Datatables::of($plan)
        ->addColumn('action', function ($plan) {
            $data1['store_selected'] = json_encode(PlanEmployee::where(['plan_employees.id_plandc' => $plan->id])
            ->join('employees','employees.id','plan_employees.id_employee')
            ->select(DB::raw("concat(employees.id,'|',employees.name) as stores_item"))
            ->get()
            ->toArray());
            $data = array(
                'id'            => $plan->id,
                'date'          => $plan->date,
                'stocklist'     => $plan->stocklist,
                'lokasi'        => $plan->lokasi
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('plan.delete', $plan->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })
        ->addColumn('planEmployee', function($plan) {
           
            $dist = PlanEmployee::where(['id_plandc'=>$plan->id])->get();
            $distList = array();
            foreach ($dist as $data) {
                $distList[] = $data->employee->name;
            }
            return rtrim(implode(',', $distList), ',');
            dd($distList);

        })->make(true);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'employee'  => 'required',
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
                        $insert = PlanDc::create([
                            'date'              => Carbon::now(),
                            'lokasi'            => $row->lokasi,
                            'stocklist'         => $row->stocklist
                        ]);
                        if ($insert) 
                            {
                                $dataStore = array();
                                foreach ($request->input('employee') as $distributor) {
                                    $dataStore[] = array(
                                        'id_employee'    => $distributor,
                                        'id_plandc'      => $insert->id,
                                    );
                                }
                                DB::table('plan_employees')->insert($dataStore); 
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

    public function exportXLS()
    {
        $PlanDc = PlanDc::orderBy('created_at', 'DESC')->get();
        $dataEmp = array();
        foreach ($PlanDc as $val) {
            $emp = PlanEmployee::where(['id_plandc'=>$val->id])->get();
            $empList = array();
            foreach ($emp as $dataEmp) {
                $empList[] = $dataEmp->employee->name;
            }
            $data[] = array(
                'Employee'          => rtrim(implode(',', $empList), ','),
                'Date'              => $val->date,
                'Lokasi'            => $val->lokasi,
                'Stocklist'          => $val->stocklist
            );
        }
        $filename = "PlanDemoCooking_".Carbon::now().".xlsx";
        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('Store', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download();
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'date'           => 'required',
            'lokasi'         => 'required',
            'stocklist'      => 'required',
            'employee'       => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $store = Plandc::find($id);
                if ($request->input('employee')) {
                    foreach ($request->input('employee') as $emp) {
                        PlanEmployee::where('id_plandc', $id)->delete();
                        $dataEmp[] = array(
                            'id_employee'       => $emp,
                            'id_plandc'         => $id,
                        );
                    }
                    DB::table('plan_employees')->insert($dataEmp);
                }
                $store->date             = $request->input('date');
                $store->lokasi           = $request->input('lokasi');
                $store->stocklist        = $request->input('stocklist');
                $store->save();
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Plan Demo Cooking!'
                ]);
        }
    }

    public function delete($id)
    {
        $plan = PlanDc::find($id);
            $plan->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
    
}
