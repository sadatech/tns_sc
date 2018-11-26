<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\Category;
use App\SubCategory;
use App\ProductCompetitor;
use App\Price;
use App\Brand;
use App\ProductPromo;
use App\ProductFokus;
use App\Filters\ProductFilters;

class ProductCompetitorController extends Controller
{
    public function getDataWithFilters(ProductFilters $filters){
        $data = ProductCompetitor::filter($filters)->get();
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
        $productCompetitor = ProductCompetitor::where('id_brand','>','0')->with('subcategory')->with('brand')->with('product');
        return Datatables::of($productCompetitor)
        ->addColumn('brand', function($productCompetitor) {
            return $productCompetitor->brand->name;
        })
        ->addColumn('subcategory', function($productCompetitor) {
            return $productCompetitor->subcategory->name;
        })
        ->addColumn('action', function ($productCompetitor) {
            $data = array(
                'id'            => $productCompetitor->id,
                'brand'         => $productCompetitor->brand->id,
                'subcategory'   => $productCompetitor->subcategory->id,
                'product'       => $productCompetitor->product->id,
                'name'          => $productCompetitor->name,
                'deskrispi'     => $productCompetitor->deskripsi
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('product-competitor.delete', $productCompetitor->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            // 'deskripsi'     => 'required',
            'subcategory'   => 'required|numeric',
            'brand'         => 'required|numeric',
            'product'    => 'required|numeric'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            ProductCompetitor::create([
                'name'              => $request->input('name'),
                'deskripsi'         => $request->input('deskripsi'),
                'id_subcategory'    => $request->input('subcategory'),
                'id_brand'          => $request->input('brand'),
                'id_product'        => $request->input('product'),
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
      $productCompetitor = ProductCompetitor::find($id);
        $productCompetitor->name          = $request->get('name');
        $productCompetitor->deskripsi     = $request->get('deskripsi');
        $productCompetitor->id_subcategory= $request->get('subcategory');
        $productCompetitor->id_brand      = $request->get('brand');
        $productCompetitor->id_product    = $request->get('product');
        $productCompetitor->save();
        return redirect()->back()
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product!'
        ]);
    }

    public function delete($id)
    {
        $productCompetitor = ProductCompetitor::find($id);
            // $prc = Price::where(['id_product' => $productCompetitor->id])->count();
            // $pf = ProductFokus::where(['id_product' => $productCompetitor->id])->count();
            // $pp = ProductPromo::where(['id_product' => $productCompetitor->id])->count();
            // if (!$prc < 1) {
            //     return redirect()->back()
            //     ->with([
            //         'type'    => 'danger',
            //         'title'   => 'Gagal!<br/>',
            //         'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain di Price, Promo, dan Target Product!'
            //     ]);
            // } else {
                $productCompetitor->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            // }
    }
}
