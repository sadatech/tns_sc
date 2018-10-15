<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\ProductFokus;
use App\Area;
use App\Product;
class ProductFokusController extends Controller
{
    public function baca()
    {
    	$data['area']       = Area::get();
        $data['product']    = Product::get();
        return view('product.fokus',$data);
    }

    public function data()
    {
        $product = ProductFokus::with(['product','area'])
        ->select('product_fokuses.*');
        return Datatables::of($product)
        ->addColumn('area', function($product) {
			if (isset($product->area)) {
				$area = $product->area->name;
			} else {
				$area = "Without Area";
			}
			return $area;
		})
        ->addColumn('action', function ($product) {
            if (isset($product->area)) {
				$area = $product->area->id;
			} else {
				$area = "Without Area";
			}
            $data = array(
                'id'            => $product->id,
                'product'     	=> $product->product->id,
                'type'      	=> $product->type,
                'area'          => $area,
                'from'          => $product->from,
                'to'          	=> $product->to
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('fokus.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'type'          => 'required',
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
            ProductFokus::create([
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
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
        $product = ProductFokus::find($id);
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
              'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus!'
          ]);
    }

    public function delete($id)
    {
        $product = ProductFokus::find($id);
            $product->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
}
