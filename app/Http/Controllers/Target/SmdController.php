<?php

namespace App\Http\Controllers\Target;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Target;
use App\Employee;
use App\Brand;
use App\Pasar;
use Auth;

class SmdController extends Controller
{
    public function baca()
    {
        $data['employee']   = Employee::get();
        $data['pasar']      = Pasar::get();
        return view('target.dc',$data);
    }

    public function data()
    {
        $product = Target::with(['pasar','employee'])
        ->select('targets.*');
        return Datatables::of($product)
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'employee'      => $product->employee->id,
                'pasar'         => $product->pasar->id,
                'value'         => $product->value,
                'valuepf'       => $product->valuepf,
                'rilis'         => $product->rilis
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('target.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'type'          => 'required',
            'employee'      => 'required|numeric',
            'pasar'         => 'required|numeric',
            'rilis'         => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            Target::create([
                'id_employee'   => $request->input('employee'),
                'id_product'    => $request->input('product'),
                'id_pasar'      => $request->input('pasar'),
                'rilis'         => $request->input('rilis'),
                'value'         => $request->input('rilis'),
                'valuepf'       => $request->input('valuepf'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
        $product = Target::find($id);
        $product->id_pasar      = $request->get('pasar');
        $product->id_employee   = $request->get('employee');
        $product->rilis         = $request->get('rilis');
        $product->value         = $request->get('value');
        $product->valuepf       = $request->get('valuepf');
        $product->save();
        return redirect()->back()
        ->with([
          'type'    => 'success',
          'title'   => 'Sukses!<br/>',
          'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus!'
      ]);
    }

    public function delete($id)
    {
        $product = Target::find($id);
        $product->delete();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
    }
}
