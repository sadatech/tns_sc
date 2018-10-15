<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use App\Price;
use App\Product;

class PriceController extends Controller
{
    public function baca()
    {
        $data['product'] = Product::get();
        return view('product.price', $data);
    }

    public function data()
    {
        $price = Price::with('product');
        return Datatables::of($price)
        ->addColumn('product', function($price) {
            return $price->product->name;
        })
        ->addColumn('category', function($price) {
            return $price->product->category->name;
        })
        ->addColumn('action', function ($price) {
            $data = array(
                'id'                => $price->id,
                'product'           => $price->product->id,
                'rilis'             => $price->rilis,
                'price'             => $price->price,
                'type_toko'         => $price->type_toko,
                'type_price'        => $price->type_price
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('price.delete', $price->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'price'         => 'required',
            'product'       => 'required|numeric',
            'Ttoko'         => 'required',
            'Tprice'        => 'required',
            'rilis'         => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            Price::create([
                'price'         => $request->input('price'),
                'id_product'    => $request->input('product'),
                'type_toko'     => $request->input('Ttoko'),
                'type_price'    => $request->input('Tprice'),
                'rilis'         => $request->input('rilis'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Price Product!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
      $price = Price::find($id);
        $price->price         = $request->get('price');
        $price->rilis         = $request->get('rilis');
        $price->type_toko     = $request->get('Ttoko');
        $price->type_price    = $request->get('Tprice');
        $price->id_product    = $request->get('product');

        $price->save();
        return redirect()->back()
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah price product!'
        ]);
    }

    public function delete($id)
    {
        $price = Price::find($id);
            $price->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
    
}
