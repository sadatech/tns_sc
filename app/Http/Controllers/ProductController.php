<?php

namespace App\Http\Controllers;
use File;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Brand;
use App\Category;
use App\Filters\ProductFilters;
use App\Price;
use App\Product;
use App\ProductFokus;
use App\ProductPromo;
use App\ProductMeasure;
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
                'product'       => $product->id_product,
                'subcategory'   => $product->subcategory->id,
                'name'          => $product->name,
                'code'          => $product->code,
                'stock_type_id' => $product->stock_type_id,
                'deskrispi'     => $product->deskripsi,
                'panel'         => $product->panel,
                'measure'       => ProductMeasure::where('id_product',$product->id)->pluck('id_measure')
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
                ProductMeasure::create([
                    'id_product' => $product->id,
                    'id_measure' => $sku_id
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

            $oldSkuUnits = $product->measure->pluck('id_measure');
            $deletedSkuUnits = $oldSkuUnits->diff($data['measure']);
            foreach ($deletedSkuUnits as $deleted_id) {
                ProductMeasure::where(['product_id' => $product->id, 'id_measure' => $deleted_id])->delete(); 
            }

            foreach ($data['measure'] as $sku_id) {
                ProductMeasure::updateOrCreate([
                    'id_product' => $product->id,
                    'id_measure' => $sku_id
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
        $jumlah = $prc + $pf + $pp;
        if (!$jumlah < 1) 
        {
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

    public function export()
    
    {
		$emp = Product::orderBy('created_at', 'DESC')
        ->get();
		foreach ($emp as $val) {
		// 	$unit = ProductUnit::where(
		// 		'product_id', $val->id
        //     )->get();
		// 	$skuList = array();
		// 	foreach($unit as $dataSku) {
		// 		$skuList[] = $dataSku->panel;
        //     }
        //     dd($skuList);
			$data[] = array(
				'brand'             => (isset($val->brand->name) ? $val->brand->name : "-"),
                'subcategory'       => $val->subcategory->name,
                'category'          => (isset($val->subcategory->category->name) ? $val->subcategory->category->name : "-"),
                'code'              => $val->code,
                'sku'               => $val->name,
                'panel'             => $val->panel,
                'stocktype'         => $val->stocktype->name
			);
		}
		$filename = "Product".Carbon::now().".xlsx";
		return Excel::create($filename, function($excel) use ($data) {
			$excel->sheet('Product', function($sheet) use ($data)
			{
				$sheet->fromArray($data);
			});
		})->download();
	}
}
