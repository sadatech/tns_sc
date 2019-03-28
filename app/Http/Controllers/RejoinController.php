<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Rejoin;
use App\Resign;
use App\Employee;
use Carbon\Carbon;
use DB;
use Auth;
use File;
use Excel;;

class RejoinController extends Controller
{
    public function baca()
    {
        return view('employee.rejoin');
    }

    public function data()
    {
        $rejoin = Employee::where([
            'isResign' => 1
        ])->with(['position', 'agency']);

        return Datatables::of($rejoin)
        ->addColumn('position', function($rejoin) {
            return $rejoin->position->name;
        })
        ->addColumn('agency', function($rejoin) {
            return $rejoin->agency->name;
        })
        ->addColumn('action', function ($rejoin) {
            $resign = Resign::where(['id_employee' => $rejoin->id])->first();
            $data = array(
                'id'               => $rejoin->id,
                'name'          => $rejoin->name,
                'nik'           => $rejoin->nik,
                'position'      => $rejoin->position->name,
                'status'        => $rejoin->status,
                'agency'        => $rejoin->agency->name,
                'resign_date'   => $resign->resign_date,
                'alasan'        => $resign->alasan,
                'penjelasan'    => $resign->penjelasan
            );
            return "<a href='#rejoin' onclick='modalRejoin(".json_encode($data).")' data-toggle='modal' class='btn btn-sm btn-block btn-success btn-square'><i class='si si-logout mr-2'></i>Rejoin</a>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $check = Employee::where([
            ['id', $request->input('employee')],
        ]);
        if ($check) {
            $insert = Rejoin::create([
                'join_date'     => $request->input('join_date'),
                'alasan'        => $request->input('alasan'),
                'id_employee'   => $request->input('employee')
            ]);
            if ($insert->id) {
                $update = Employee::find($request->input('employee'));
                $update->isResign = 0;
                if ($update->save()) {
                    return redirect()->back()
                    ->with([
                        'type'      => 'success',
                        'title'     => 'Sukses!<br/>',
                        'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil melakukan rejoin pegawai!'
                    ]);
                } else {
                    return redirect()->back()
                    ->with([
                        'type'      => 'danger',
                        'title'     => 'Gagal!<br/>',
                        'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil melakukan rejoin pegawai!'
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

    public function export()
    {
        $rejoin = Employee::where('isResign', 1)->orderBy('created_at', 'DESC')->get();
        $data = array();
        foreach ($rejoin as $val) {
            $data[] = array(
                'Nik'           => $val->nik,
                'Employee'      => $val->name,
                'Agency'        => $val->agency->name,
                'Position'      => $val->position->name
            );
        }
        $filename = "rejoin_".Carbon::now().".xlsx";
        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('Rejoin', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download();
    }
}