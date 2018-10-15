<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Target;
use App\Employee;
use App\Brand;
use App\Store;
use Auth;

class TargetController extends Controller
{
    public function baca()
    {
    	$data['employee']       = Employee::get();
        $data['store']    = Store::get();
        return view('product.target',$data);
    }

    public function data()
    {
        $product = Target::with(['store','employee'])
        ->select('targets.*');
        return Datatables::of($product)
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'employee'     	=> $product->employee->id,
                'store'      	=> $product->store->id,
                'type'      	=> $product->type,
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
            'store'         => 'required|numeric',
            'rilis'         => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            Target::create([
                'type'          => $request->input('type'),
                'id_employee'   => $request->input('employee'),
                'id_product'    => $request->input('product'),
                'id_store'      => $request->input('store'),
                'rilis'         => $request->input('rilis'),
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
            $product->type          = $request->get('type');
            $product->id_store    = $request->get('store');
            $product->id_employee       = $request->get('employee');
            $product->rilis          = $request->get('rilis');
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
