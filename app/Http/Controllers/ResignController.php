<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Resign;
use App\ResignStore;
use App\Employee;
use App\EmployeeStore;
use App\Store;
use DB;
use Auth;

class ResignController extends Controller
{
    public function baca()
    {
        return view('employee.resign');
    }

    public function data()
    {
        $resign = Employee::where([
            'isResign' => 0
        ])->with(['position']);
        
        return Datatables::of($resign)
        ->addColumn('action', function ($resign) {
            $data = array(
                'id'        => $resign->id,
                'name'      => $resign->name,
                'nik'       => $resign->nik,
                'position'  => $resign->position->name,
                'status'    => $resign->status,
                'joindate'  => $resign->joinAt
            );
            return "<a href='#resign' onclick='modalResign(".json_encode($data).")' data-toggle='modal' class='btn btn-sm btn-block btn-danger btn-square'><i class='si si-logout mr-2'></i>Resign</a>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $check = Employee::where([
            ['id', $request->input('employee')],
        ]);
        if ($check) {
            $reason = rtrim(implode(',', $request->input('reason')), ',');
            $insert = Resign::create([
                'resign_date'   => $request->input('submission'),
                'effective'     => $request->input('effective'),
                'alasan'        => $reason,
                'penjelasan'    => $request->input('optional'),
                'id_employee'   => $request->input('employee')
            ]);
            if ($insert->id) {
                $store = EmployeeStore::where(['id_employee' => $request->input('employee')])->get();
                // dd($store);
                $storeAll = array();
                foreach ($store as $data) {
                    $storeAll[] = array(
                        'id_store'  => $data->id_store,
                        'id_resign' => $insert->id 
                    );
                }
                $save = DB::table('resign_stores')->insert($storeAll);
                if ($save) {
                    $update = Employee::find($request->input('employee'));
                    $update->isResign = 1;
                    if ($update->save()) {
                        return redirect()->back()
                        ->with([
                            'type'      => 'success',
                            'title'     => 'Sukses!<br/>',
                            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil melakukan resign pegawai!'
                        ]);
                    } else {
                        return redirect()->back()
                        ->with([
                            'type'      => 'danger',
                            'title'     => 'Gagal!<br/>',
                            'message'   => '<i class="em em-confetti_ball mr-2"></i>Gagal melakukan resign pegawai!'
                        ]);
                    }
                } else {
                    return redirect()->back()
                    ->with([
                        'type'      => 'danger',
                        'title'     => 'Gagal!<br/>',
                        'message'   => '<i class="em em-confetti_ball mr-2"></i>Gagal melakukan resign pegawai!'
                    ]);
                }
            }

        } else {
            return redirect()->back()
            ->with([
                'type'      => 'danger',
                'title'     => 'Gagal!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Kamu tidak diizinkan!'
            ]);
        }
    }
}