<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Filters\ProductFilters;
use App\Price;
use App\Product;
use App\ProductFokus;
use App\ProductPromo;
use App\ProductUnit;
use App\SubCategory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;

class ProductController extends Controller
{
    public function getDataWithFilters(ProductFilters $filters){
        $data = Product::filter($filters)->get();
        return $data;
    }

   public function baca()
   {
       return view('product.product');
   }

   public function data()
    {
        $product = Product::where('id_brand','1')->with('subcategory')->with('brand')->with('stockType')->with('sku_units');
        return Datatables::of($product)
        ->addColumn('brand', function($product) {
            return $product->brand->name;
        })
        ->addColumn('subcategory', function($product) {
            return $product->subcategory->name;
        })
        ->addColumn('stockType', function($product) {
            return $product->stockType->name;
        })
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'subcategory'   => $product->subcategory->id,
                'name'          => $product->name,
                'code'          => $product->code,
                'stock_type_id' => $product->stock_type_id,
                'deskrispi'     => $product->deskripsi,
                'panel'         => $product->panel,
                'sku_units'     => $product->sku_units->pluck('sku_unit_id')
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('product.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (($validator = Product::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use($data) {
            $product = Product::create($data);
            foreach ($data['sku_units'] as $sku_id) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'sku_unit_id' => $sku_id
                ]);
            }
        });

        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Product!'
        ]);
    }

    public function update(Request $request, $id) 
    {
        $product = Product::findOrFail($id);
        $data = $request->all();

        if (($validator = Product::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use($product, $data) {
            $product->fill($data)->save();

            $oldSkuUnits = $product->sku_units->pluck('sku_unit_id');
            $deletedSkuUnits = $oldSkuUnits->diff($data['sku_units']);
            foreach ($deletedSkuUnits as $deleted_id) {
                ProductUnit::where(['product_id' => $product->id, 'sku_unit_id' => $deleted_id])->delete(); 
            }

            foreach ($data['sku_units'] as $sku_id) {
                ProductUnit::updateOrCreate([
                    'product_id' => $product->id,
                    'sku_unit_id' => $sku_id
                ]);
            }
        });

        return redirect()->back()->with([
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
