<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ProductPromo;
use Yajra\Datatables\Datatables;
use App\Area;
use App\Product;

class ProductPromoController extends Controller
{
    public function baca()
    {
        $data['area']       = Area::get();
        $data['product']    = Product::get();
        return view('product.promo', $data);
    }

    public function data()
    {
        $product = ProductPromo::with(['product','area']);
        return Datatables::of($product)
        ->addColumn('product', function($product) {
            return $product->product->name;
        })
        ->addColumn('area', function($product) {
            return $product->area->name;
        })
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'product'     	=> $product->product->id,
                'type'      	=> $product->type,
                'area'          => $product->area->id,
                'from'          => $product->from,
                'to'          	=> $product->to
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('promo.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'type'          => 'required',
            'area'          => 'required|numeric',
            'product'       => 'required|numeric',
            'from'          => 'required',
            'to'            => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            ProductPromo::create([
                'type'          => $request->input('type'),
                'id_area'       => $request->input('area'),
                'id_product'    => $request->input('product'),
                'from'          => $request->input('from'),
                'to'            => $request->input('to'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk promo!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
        $product = ProductPromo::find($id);
            $product->type          = $request->get('type');
            $product->id_product    = $request->get('product');
            $product->id_area       = $request->get('area');
            $product->from          = $request->get('from');
            $product->to            = $request->get('to');
            $product->save();
            return redirect()->back()
            ->with([
              'type'    => 'success',
              'title'   => 'Sukses!<br/>',
              'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product promo!'
          ]);
    }

    public function delete($id)
    {
        $product = ProductPromo::find($id);
            $product->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
}
