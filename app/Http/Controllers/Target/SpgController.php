<?php

namespace App\Http\Controllers\Target;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\TargetGtc;
use App\Employee;
use App\Brand;
use App\Pasar;
use Auth;

class SpgController extends Controller
{
    public function baca()
    {
        $data['employee']   = Employee::where('id_position', 3)->get();
        $data['pasar']      = Pasar::get();
        return view('target.spg',$data);
    }

    public function data()
    {
        $target = TargetGtc::with(['pasar','employee'])
        ->select('targets.*');
        return Datatables::of($target)
        ->addColumn('action', function ($target) {
            $data = array(
                'id'            => $target->id,
                'employee'      => $target->employee->id,
                'pasar'         => $target->pasar->id,
                'value'         => $target->value,
                'valuepf'       => $target->valuepf,
                'rilis'         => $target->rilis
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('target.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'employee'      => 'required|numeric',
            'pasar'       => 'required|numeric',
            'rilis'         => 'required',
            'value'         => 'required',
            'valuepf'       => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            TargetGtc::create([
                'id_employee'   => $request->input('employee'),
                'id_pasar'      => $request->input('pasar'),
                'rilis'         => $request->input('rilis'),
                'value'         => $request->input('value'),
                'valuepf'       => $request->input('valuepf'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk target!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
        $target = TargetGtc::find($id);
        $target->id_pasar      = $request->get('pasar');
        $target->id_employee   = $request->get('employee');
        $target->rilis         = $request->get('rilis');
        $target->value         = $request->get('value');
        $target->valuepf       = $request->get('valuepf');
        if ($target->save()) {
            return redirect()->back()->with([
                'type'    => 'success',
                'title'   => 'Sukses!<br/>',
                'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus!'
            ]);
        } else {
            return redirect()->route('employee')
            ->with([
                'type'      => 'danger',
                'title'     => 'Terjadi Kesalahan!<br/>',
                'message'   => '<i class="em em-thinking_face mr-2"></i>Gagal mengupdate data!'
            ]);
        }
    }

    public function delete($id)
    {
        $product = TargetGtc::find($id);
        if ($product->delete()) {
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
        } else {
            return redirect()->route('employee')
            ->with([
                'type'      => 'danger',
                'title'     => 'Terjadi Kesalahan!<br/>',
                'message'   => '<i class="em em-thinking_face mr-2"></i>Gagal menghapus data!'
            ]);
        }
    }
}
