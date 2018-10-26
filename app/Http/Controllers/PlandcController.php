<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\PlanDc;

class PlandcController extends Controller
{
    public function read()
    {
        return view('plandc.plandc');
    }

    public function data()
    {
        $plan = PlanDc::with('planEmployee')
        ->select('plan_dcs.*');
        return Datatables::of($plan)
        ->addColumn('action', function ($plan) {
            return "<a href=".route('ubah.plan', $plan->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('plan.delete', $plan->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' =>   'required|'
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
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use ($id_company)
                {
                    foreach($results as $row)
                    {
                        echo "$row<hr>";
                        $insert = PlanDc::create([
                            'date'              => $row->date,
                            'lokasi'            => $row->lokasi,
                            'stocklist'         => $row->stocklist
                        ]);
                        if ($insert) 
                            {
                                $dataDc = array();
                                foreach ($request->input('employee') as $emp) {
                                    $dataDc[] = array(
                                        'id_employee'       => $emp,
                                        'id_plandc'         => $insert->id
                                    );
                                }
                                DB::table('pland_dc')->insert($dataDc); 
                            }
                        // if ($insert) {
                        //     $dataPlan = array();
                        //     $listPlan = explode(",", $row->distributor);
                        //     foreach ($listPlan as $plan) {
                        //         $dataPlan[] = array(
                        //             'id_employee'       => $this->findEmployee($plan),
                        //             'id_plandc'         => $insert->id
                        //         );
                        //     }
                        //     DB::table('plan_employees')->insert($dataPlan);
                        // }
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

    // public function findEmployee($data)
    // {
    //     $dataEmp = Employee::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data))."'");
    //     if ($dataEmp->count() == 0) {
    //         $Emp = Distributor::create([
    //             'name'       => $data,
    //         ]);
    //         if ($Emp) {
    //             $id_Emp = $Emp->id;
    //         }
    //     } else {
    //         $id_Emp = $dataEmp->first()->id;
    //     }
    //     return $id_Emp;
    // }
}
