<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use App\Product;
use App\Category;
use App\SubCategory;
use App\Brand;
use App\ProductStockType;

class ProductStockTypeController extends Controller
{
    public function baca()
    {
        return view('product.stock-type');
    }

    public function data()
    {
        $stockType = ProductStockType::get();
        return Datatables::of($stockType)
        ->addColumn('action', function ($stockType) {
            $data = array(
                'id'        => $stockType->id,
                'name'      => $stockType->name,
                'quantity'      => $stockType->quantity
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>"
            // "<button data-url=".route('stock-type.delete', $stockType->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>"
            ;
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            'quantity'      => 'required|integer',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            Category::create([
                'name'          => $request->input('name'),
                'quantity'         => $request->input('quantity'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Category Product!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
        // return response()->json($request);
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            'quantity'      => 'required|integer',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $stockType = ProductStockType::find($id);
            $stockType->name         = $request->get('name');
            $stockType->quantity     = $request->get('quantity');
            $stockType->save();
            return redirect()->back()
            ->with([
                'type'    => 'success',
                'title'   => 'Sukses!<br/>',
                'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah category product!'
            ]);
        }
    }

    public function delete($id) 
    {
        $stockType = ProductStockType::find($id);
            $duct = Product::where(['stock_type_id' => $stockType->id])->count();
            if (!$duct < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lainnya di Product dan Price!'
                ]);
            } else {
                $stockType->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            }
    }
}
