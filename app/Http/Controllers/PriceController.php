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
        $data = $request->all();
        if (($validator = Price::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if (Price::hasActivePF($data)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Price sudah ada!';
        } else {
            DB::transaction(function () use($data) {
                $product = Price::create($data);
            });
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah price!';
        }
        return redirect()->back()->with($this->alert);
    }

    public function update(Request $request, $id) 
    {
        $product = Price::findOrFail($id);
        $data = $request->all();
        if (($validator = Price::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if (Price::hasActivePF($data, $product->id)) {
                $this->alert['type'] = 'warning';
                $this->alert['title'] = 'Warning!<br/>';
                $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Price sudah ada!';
            } else {
            DB::transaction(function () use($product, $data) {

                $product->fill($data)->save();
            });
            $this->alert['type'] = 'success';
            $this->alert['title'] = 'Berhasil!<br/>';
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah produk fokus!';
        }
        return redirect()->back()->with($this->alert);
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
