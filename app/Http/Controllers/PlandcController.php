<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\PlanDc;
use App\PlanEmployee;
use App\ReportInventori;
use DB;
use Auth;
use File;
use Excel;
use Validator;
use App\Employee;
use App\PropertiDc;
use Carbon\Carbon;

class PlandcController extends Controller
{
    public function read()
    {
        $data['employee']       = Employee::where('id_position', 5 )->get();
        return view('plandc.plandc', $data);
    }

    public function readProperti()
    {
        return view('plandc.properti');
    }

    public function dataProperti()
    {
        $properti = PropertiDc::get();
        return Datatables::of($properti)
        ->addColumn('action', function ($properti) {
            $data = array(
                'id'            => $properti->id,
                'item'          => $properti->item
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('properti.delete', $properti->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->make(true);

    }

    public function readUpdate($id)
    {
        $data['plan'] 		    = PlanDc::where(['id' => $id])->first();
        $data['employee']       = Employee::where('id_position', 5 )->get();
        $data['employee_selected'] = json_encode(PlanEmployee::where(['plan_employees.id_plandc' => $id])
            ->join('employees','employees.id','plan_employees.id_employee')
            ->select(DB::raw("concat(employees.id,'|',employees.name) as employees_item"))
            ->get()
            ->toArray());
        return view('plandc.update', $data);
    }

    public function data()
    {
        $plan = PlanDc::with('planEmployee')
        ->select('plan_dcs.*');
        return Datatables::of($plan)
        ->addColumn('action', function ($plan) {
            return "<a href=".route('ubah.plan', $plan->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('plan.delete', $plan->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        
        })
        ->addColumn('planEmployee', function($plan) {
           
            $dist = PlanEmployee::where(['id_plandc'=>$plan->id])->get();
            $distList = array();
            foreach ($dist as $data) {
                $distList[] = $data->employee->name;
            }
            return rtrim(implode(',', $distList), ',');

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
                
                Excel::filter('chunk')
                ->selectSheetsByIndex(1)
                ->load($file)
                ->formatDates(true)
                ->chunk(250, function($results) use ($request)
                {
                    foreach($results as $row)   
                    {
                        $rowRules = [
                            'date'   => 'required',
                            'plan'   => 'required'
                        ];
                        $validator = Validator::make($row->toArray(), $rowRules);
                        if ($validator->fails()) {
                            continue;
                        } else {
                            // $data1 = Employee::where(['id' => $request->input('employee')])->first();
                            // $data2 = PlanDc::whereRaw("TRIM(UPPER(lokasi)) = '". trim(strtoupper($row['lokasi']))."'");
                            // $check = PlanEmployee::where(['id_employee' => $data1->id])->where(['id_plandc' => $data2->id]);
                            // if($check->count() < 1 ) {
                            // dd($check);
                            $insert = PlanDc::create([
                                'date'              => \PHPExcel_Style_NumberFormat::toFormattedString($row['date'], 'YYYY-MM-DD'),
                                'plan'              => $row['plan'],
                                'stocklist'         => (isset($row->stockist) ? $row->stockist : "-"),
                                'channel'           => (isset($row->channel) ? $row->channel : "-")
                            ]);
                            if (!empty($insert)) 
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

    public function importProperti(Request $request)
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
                
                Excel::filter('chunk')
                ->selectSheetsByIndex(0)
                ->load($file)
                ->formatDates(true)
                ->chunk(250, function($results) use ($request)
                {
                    foreach($results as $row)   
                    {
                        $rowRules = [
                            'item'   => 'required'
                        ];
                        $validator = Validator::make($row->toArray(), $rowRules);
                        if ($validator->fails()) {
                            continue;
                        } else {
                            $insert = PropertiDc::create([
                                'item'              => $row['item']
                            ]);
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
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil import Properti Dc!'
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
        $PlanDc = PlanDc::orderBy('created_at', 'DESC');
        if ($PlanDc->count() > 0) {
            $dataEmp = array();
            foreach ($PlanDc->get() as $val) {
                $emp = PlanEmployee::where(['id_plandc'=>$val->id])->get();
                $empList = array();
                foreach ($emp as $dataEmp) {
                    $empList[] = $dataEmp->employee->name;
                }
                $data[] = array(
                    'Employee'          => rtrim(implode(',', $empList), ','),
                    'Date'              => $val->date,
                    'Plan'              => $val->plan,
                    'Stocklist'         => (isset($val->stocklist) ? $val->stocklist : "-"),
                    'Channel'           => (isset($val->channel) ? $val->channel : "-")
                );
            }
            $filename = "PlanDemoCooking_".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('PlanDemoCooking', function($sheet) use ($data)
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

    public function exportProperti()
    {
        $PlanDc = PropertiDc::orderBy('created_at', 'DESC');
        if ($PlanDc->count() > 0) {
            $dataEmp = array();
            foreach ($PlanDc->get() as $val) {
                $data[] = array(
                    'Item'              => $val->item
                );
            }
            $filename = "PropertiDc".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('PropertiDc', function($sheet) use ($data)
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

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'date'           => 'required',
            'plan'           => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $store = PlanDc::find($id);
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
            $store->plan             = $request->input('plan');
            $store->stocklist        = $request->input('stocklist');
            $store->channel          = $request->input('channel');
            $store->save();
            return redirect()->route('planDc')
            ->with([
                'type'    => 'success',
                'title'   => 'Sukses!<br/>',
                'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Plan Demo Cooking!'
            ]);
        }
    }

    public function storeProperti(Request $request)
    {
        $data=$request->all();
        $limit=[
            'item'          => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = PropertiDc::whereRaw("TRIM(UPPER(item)) = '". strtoupper($request->input('item'))."'")->count();
            if ($check < 1) {
                PropertiDc::create([
                    'item'       => $request->input('item'),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah properti Dc!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'warning',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Properti DC sudah ada!'
                ]);
            }
        }
    }

    public function updateProperti(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'item'           => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = PropertiDc::whereRaw("TRIM(UPPER(item)) = '". strtoupper($request->input('item'))."'")->count();
            if ($check < 1) {
                $store = PropertiDc::find($id);
                $store->item             = $request->input('item');
                $store->save();
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Properti Dc!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'warning',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Properti DC sudah ada!'
                ]);
            }
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

    public function deleteProperti($id)
    {
        $plan = PropertiDc::find($id);
            $plan->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }


    public function readInventori()
    {
        return view('plandc.inventori');
    }


    public function inventoriDC($id_employee)
    {
        $data = ReportInventori::where("id_employee", $id_employee)->get();
        return Datatables::of(collect($data))
        ->addColumn("employee", function($item){
            return Employee::where("id", $item->id_employee)->first()->name;
        })
        ->addColumn("item", function($item){
            return PropertiDc::where("id", $item->id_properti_dc)->first()->item;
        })
        ->addColumn("dokumentasi", function($item){
            return (isset($item->photo) ? "<img src='".asset($item->photo)."' style='min-width: 149px;max-width: 150px;'>" : "-");
        })
        ->rawColumns(["dokumentasi"])
        ->make(true);
    }

    public function inventoriDCAdd(Request $req)
    {
        $PropertiDcs = PropertiDc::get();
        $result = DB::transaction(function() use ($PropertiDcs, $req){
            foreach ($PropertiDcs as $PropertiDc)
            {
                $countReportInventori = ReportInventori::where("id_employee", $req->id_employee)->where("id_properti_dc", $PropertiDc->id)->count();
                if ($countReportInventori == 0)
                {
                    ReportInventori::create([
                        "no_polisi"       => strtoupper($req->no_polisi),
                        "id_employee"     => $req->id_employee,
                        "id_properti_dc"  => $PropertiDc->id,
                        "quantity"        => 0,
                        "actual"          => 0
                    ]);
                }
            }
        });

        return redirect(route('inventoriDc'))->with([
                'type'   => 'success',
                'title'  => 'Berhasil<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Data berhasil diperbarui!'
        ]);
    }

    // use \App\Traits\ExportDCReportInventoriTrait;

    public function inventoriDCExportXLS()
    {
        // return $this->DCReportInventoriExportTrait("cc");
        $result = DB::transaction(function(){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "Demo Cooking - Report Inventori (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportDCReportInventoriJob($JobTrace, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    
}
