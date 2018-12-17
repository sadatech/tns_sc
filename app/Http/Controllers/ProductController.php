<?php

namespace App\Http\Controllers;
use File;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Filters\ProductFilters;
use App\Price;
use App\Product;
use App\ProductFokus;
use App\ProductUnit;
use App\ProductPromo;
use App\ProductMeasure;
use App\SubCategory;
use App\Category;
use App\Brand;
use App\ProductStockType;
use App\SkuUnit;
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


    public function getProductByCategory($param){

        $data = Product::join('sub_categories','products.id_subcategory','sub_categories.id')
                        ->join('categories','sub_categories.id_category', 'categories.id')
                        ->where('categories.id',$param)
                        ->select('products.*')->get();

        // return response()->json($data);
        return $data;

    }

   public function baca()
   {
       return view('product.product');
   }

   public function data()
    {
        $product = Product::where('id_brand','1')->with('subcategory')->with('brand')->with('stockType')->with('sku_units')
        ->select('products.*');
        return Datatables::of($product)
        ->addColumn('brand', function($product) {
            return $product->brand->name;
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
                'carton'        => $product->carton,
                'pack'          => $product->pack,
                'pcs'           => $product->pcs,
               
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
            // $measure = $data['measure'];
            // unset($data['measure']);
            $product = Product::create($data);
            // foreach ($measure as $sku_id) {
            //     ProductMeasure::create([
            //         'id_product' => $product->id,
            //         'id_measure' => $sku_id
            //     ]);
            // }
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

        // return $data;

        if (($validator = Product::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use($product, $data) {
            // $measure = $data['measure'];
            // unset($data['measure']);

            $product->fill($data)->save();

            // $oldSkuUnits = $product->measure->pluck('id_measure');
            // $deletedSkuUnits = $oldSkuUnits->diff($measure);
            // foreach ($deletedSkuUnits as $deleted_id) {
            //     ProductMeasure::where(['product_id' => $product->id, 'id_measure' => $deleted_id])->delete(); 
            // }

            // foreach ($measure as $sku_id) {
            //     ProductMeasure::updateOrCreate([
            //         'id_product' => $product->id,
            //         'id_measure' => $sku_id
            //     ]);
            // }
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
        $jumlah = $prc + $pf;
        if (!$jumlah < 1) 
        {
            return redirect()->back()
            ->with([
                'type'    => 'danger',
                'title'   => 'Gagal!<br/>',
                'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain di Price dan ProductFokus!'
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
			$data[] = array(
				'brand'             => (isset($val->brand->name) ? $val->brand->name : "-"),
                'subcategory'       => $val->subcategory->name,
                'category'          => (isset($val->subcategory->category->name) ? $val->subcategory->category->name : "-"),
                'code'              => $val->code,
                'sku'               => $val->name,
                'panel'             => $val->panel,
                'stocktype'         => $val->stocktype->name,
                'Carton'            => (isset($val->carton) ? $val->carton : "-"),
                'Pack'              => (isset($val->pack) ? $val->pack : "-"),
                'PCS'               => (isset($val->pcs) ? $val->pcs : "1")
			);
		}
		$filename = "Product_".Carbon::now().".xlsx";
		return Excel::create($filename, function($excel) use ($data) {
			$excel->sheet('Product', function($sheet) use ($data)
			{
				$sheet->fromArray($data);
			});
		})->download();
    }
    
    public function import(Request $request)
    {
        $this->validate($request, [
            'file' =>   'required'
        ]);

        $transaction = DB::transaction(function () use ($request) {
            $file = Input::file('file')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($extension != 'xlsx' && $extension !=  'xls') {
                return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
            }
            if($request->hasFile('file')){
                $file = $request->file('file')->getRealPath();
                $ext = '';
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results)
                {
                    foreach($results as $row)
                    {
                        echo "$row<hr>";
                        $dataProduct['subcategory_name']    = $row->subcategory;
                        $dataProduct['category_name']       = $row->category;
                        $id_subcategory = $this->findSub($dataProduct);

                        $data1 = SubCategory::where(['id' => $id_subcategory])->first();
                        $check = Product::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->sku))."'")
                        ->where(['id_subcategory' => $data1->id])->count();
                        if ($check < 1) {
                            $getType = ProductStockType::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->type))."'")->first()->id;
                            $insert = Product::create([
                                'id_brand'          => 1,
                                'id_subcategory'    => $id_subcategory,
                                'code'              => $row->code,
                                'name'              => $row->sku,
                                'carton'            => (isset($row->carton) ? $row->carton : "-"),
                                'pack'              => (isset($row->pack) ? $row->pack : "1"),
                                'pcs'               => 1,
                                'stock_type_id'     => ($getType ? $getType : 1),
                                'panel'             => ($row->panel ? $row->panel : "yes")
                            ]);
                            // if (!empty($insert))
                            //     $dataSKu = array();
                            //     $listSku = explode(",", $row->unit);
                            //     foreach ($listSku as $sku) {
                            //         $dataSku[] = array(
                            //             'sku_unit_id'    		=> $this->findSku($sku, $row->value),
                            //             'product_id'          	=> $insert->id,
                            //         );
                            //     }
                            //     DB::table('product_units')->insert($dataSku);                
                        } else {
                            return false;
                        }
                    }
                },false);
            }
            return 'success';
        });

        if ($transaction == 'success') {
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil import!'
            ]);
        }else{
            return redirect()->back()
            ->with([
                'type'    => 'danger',
                'title'   => 'Gagal!<br/>',
                'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
            ]);
        }
    }

    // public function findSku($data, $value)
    // {
    //     $dataSku = SkuUnit::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data))."'");
    //     if ($dataSku->count() == 0) {
    //         $sku = SkuUnit::create([
    //             'name'       	         => $data,
	// 			'conversion_value'       => $value
    //         ]);
    //         if ($sku) {
    //             $id_sku = $sku->id;
    //         }
    //     } else {
    //         $id_sku = $dataSku->first()->id;
    //     }
    //     return $id_sku;
    // }

    public function findSub($data)
    {
        $dataSu = SubCategory::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['subcategory_name']))."'");
        if ($dataSu->count() == 0) {
            
            $dataCategory['category_name']  = $data['category_name'];
            $id_category = $this->findCategory($dataCategory);

            $sub = SubCategory::create([
              'name'            => $data['subcategory_name'],
              'id_category'     => $id_category,
            ]);
            $id_sub = $sub->id;
        }else{
            $id_sub = $dataSu->first()->id;
        }
      return $id_sub;
    }

    public function findCategory($data)
    {
        $dataCategory = Category::where('name','like','%'.trim($data['category_name']).'%');
        if ($dataCategory->count() == 0) {
            
            $region = Category::create([
              'name'           => $data['category_name'],
              'description'    => "-"
            ]);
            $id_category = $region->id;
        }else{
            $id_category = $dataCategory->first()->id;
        }
      return $id_category;
    }
}
