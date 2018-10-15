<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\Category;
use App\SubCategory;
use App\Product;
use App\Price;
use App\Brand;
use App\ProductPromo;
use App\ProductFokus;
use App\Filters\ProductFilters;

class ProductCompetitorController extends Controller
{
    public function getDataWithFilters(ProductFilters $filters){
        $data = Product::filter($filters)->get();
        return $data;
    }

   public function baca()
   {
       $data['brand'] = Brand::get();
       $data['subcategory'] = SubCategory::get();
       return view('product.productcompetitor', $data);
   }

   public function data()
    {
        $product = Product::where('id_brand','>','1')->with('subcategory')->with('brand');
        return Datatables::of($product)
        ->addColumn('brand', function($product) {
            return $product->brand->name;
        })
        ->addColumn('subcategory', function($product) {
            return $product->subcategory->name;
        })
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'brand'         => $product->brand->id,
                'subcategory'   => $product->subcategory->id,
                'name'          => $product->name,
                'deskrispi'     => $product->deskripsi,
                'panel'         => $product->panel
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('product.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            // 'deskripsi'     => 'required',
            'panel'         => 'required',
            'subcategory'   => 'required|numeric',
            'brand'         => 'required|numeric'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            Product::create([
                'name'              => $request->input('name'),
                'deskripsi'         => $request->input('deskripsi'),
                'id_subcategory'    => $request->input('subcategory'),
                'id_brand'          => $request->input('brand'),
                'panel'          => $request->input('panel'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Product!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
      $product = Product::find($id);
        $product->name          = $request->get('name');
        $product->deskripsi     = $request->get('deskripsi');
        $product->id_subcategory= $request->get('subcategory');
        $product->id_brand      = $request->get('brand');
        $product->panel      = $request->get('panel');
        $product->save();
        return redirect()->back()
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product!'
        ]);
    }

    public function delete($id)
    {
        $product = Product::find($id);
            $prc = Price::where(['id_product' => $product->id])->count();
            $pf = ProductFokus::where(['id_product' => $product->id])->count();
            $pp = ProductPromo::where(['id_product' => $product->id])->count();
            if (!$prc < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain di Price, Promo, dan Target Product!'
                ]);
            } else {
                $product->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            }
    }
}
