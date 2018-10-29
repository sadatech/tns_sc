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
            return $price->product->subCategory->name;
        })
        ->addColumn('action', function ($price) {
            $data = array(
                'id'                => $price->id,
                'product'           => $price->product->id,
                'rilis'             => $price->rilis,
                'price'             => $price->price,
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('price.delete', $price->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $limit=[
            'price'         => 'required',
            'id_product'    => 'required|numeric',
            'rilis'         => 'required'
        ];
        $validator = Validator($request->all(), $limit);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $priceModel = Price::firstOrNew([
                'id_product' => $request->id_product,
                'rilis' => $request->rilis,
            ]);
            $priceModel->price = $request->price;

            $message = '<i class="em em-confetti_ball mr-2"></i>';
            $message .= !$priceModel->isNewRecord() ? 'Berhasil memperbarui Price Product!' : 'Berhasil menambah Price Product!';

            $priceModel->save();

            return redirect()->back()->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> $message
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
      $price = Price::find($id);
        $price->price         = $request->get('price');
        $price->rilis         = $request->get('rilis');
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
