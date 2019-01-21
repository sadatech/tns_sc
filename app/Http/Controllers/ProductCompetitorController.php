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
use App\Product;
use App\Filters\ProductFilters;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use File;
use Excel;

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
        ->addColumn('sasa_product', function($productCompetitor) {

            $sasa_product = (isset($productCompetitor->product->name)) ? $productCompetitor->product->name : '';

                return $sasa_product;
        })
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
                'subcategory'   => (isset($productCompetitor->product->id)) ? $productCompetitor->product->id : '',
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


    public function export()
    
    {
        $emp = ProductCompetitor::orderBy('created_at', 'DESC')
        ->get();
        foreach ($emp as $val) {
            $data[] = array(
                'category'          => (isset($val->subcategory->category->name) ? $val->subcategory->category->name : "-"),
                'subcategory'       => $val->subcategory->name,
                'brand'             => (isset($val->brand->name) ? $val->brand->name : "-"),
                'sku'               => $val->name,
            );
        }
        $filename = "ProductCompetitor_".Carbon::now().".xlsx";
        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('ProductCompetitor', function($sheet) use ($data)
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
                        if ($row->category != null) {
                            echo "$row<hr>";
                            $dataProduct['subcategory_name']    = $row->subcategory;
                            $dataProduct['category_name']       = $row->category;
                            $dataProduct['product_name']       = $row->product;
                            $id_product = $this->findProduct($dataProduct);

                            $dataBrand['brand_name']       = $row->brand;
                            $id_brand = $this->findBrand($dataBrand);

                            $dataCategory['subcategory_name']    = $row->subcategory;
                            $dataCategory['category_name']       = $row->category;
                            $id_subcategory = $this->findSub($dataCategory);

                            $check = ProductCompetitor::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->sku))."'")->where(['id_brand' => $id_brand])
                            ->where(['id_product' => $id_product])->where(['id_subcategory' => $id_subcategory])->count();
                            if ($check < 1) {
                                $insert = ProductCompetitor::create([
                                    'id_brand'          => $id_brand,
                                    'id_product'        => $id_product,
                                    'id_subcategory'    => $id_subcategory,
                                    'name'              => $row->sku,
                                ]);
                                // if (!empty($insert))
                                //     $dataSKu = array();
                                //     $listSku = explode(",", $row->unit);
                                //     foreach ($listSku as $sku) {
                                //         $dataSku[] = array(
                                //             'sku_unit_id'            => $this->findSku($sku, $row->value),
                                //             'product_id'             => $insert->id,
                                //         );
                                //     }
                                //     DB::table('product_units')->insert($dataSku);                
                            } else {
                                return false;
                            }
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

    public function findBrand($data)
    {
        $dataBrand = Brand::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['brand_name']))."'");
        if ($dataBrand->count() == 0) {

            $brand = Brand::create([
              'name'            => $data['brand_name'],
            ]);
            $id_brand = $brand->id;
        }else{
            $id_brand = $dataBrand->first()->id;
        }
      return $id_brand;
    }

    public function findProduct($data)
    {
        $dataSku = Product::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['product_name']))."'");
        if ($dataSku->count() == 0) {
            $dataSu = SubCategory::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['subcategory_name']))."'");
            if ($dataSu->count() == 0) {
            
                $dataSubCategory['category_name']  = $data['category_name'];
                $dataSubCategory['subcategory_name']  = $data['subcategory_name'];
                $id_subcategory = $this->findSub($dataSubCategory);

                            $insert = Product::create([
                                'id_brand'          => 1,
                                'id_subcategory'    => $id_subcategory,
                                'code'              => "-",
                                'name'              => $data['product_name'],
                                'carton'            => "-",
                                'pack'              => "1",
                                'pcs'               => 1,
                                'stock_type_id'     => 1,
                                'panel'             => "yes"
                            ]);
                            $id_product = $insert->id;
            }else{
                $check = Product::where(['id_subcategory' => $dataSu->id])->first();
                if ($check->count() == 0) {

                            $insert = Product::create([
                                'id_brand'          => 1,
                                'id_subcategory'    => $dataSu->id,
                                'code'              => "-",
                                'name'              => $data['product_name'],
                                'carton'            => (isset($row->carton) ? $row->carton : "-"),
                                'pack'              => (isset($row->pack) ? $row->pack : "1"),
                                'pcs'               => 1,
                                'stock_type_id'     => ($getType ? $getType : 1),
                                'panel'             => ($row->panel ? $row->panel : "yes")
                            ]);
                            $id_product = $insert->id;
                }else{
                    $id_product = $check->first()->id;
                }
            }
        }else{
            $id_product = $dataSku->first()->id;
        }
      return $id_product;
    }

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
            
            $categoryInput = Category::create([
              'name'           => $data['category_name'],
              'description'    => "-"
            ]);
            $id_category = $categoryInput->id;
        }else{
            $id_category = $dataCategory->first()->id;
        }
      return $id_category;
    }
}
