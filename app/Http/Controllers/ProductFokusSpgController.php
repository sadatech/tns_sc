<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\SubCategory;
use App\ProductFokusSpg;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use App\SkuUnit;
use App\ProductStockType;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\Datatables\Datatables;

class ProductFokusSpgController extends Controller
{
    private $alert = [
        'type' => 'success',
        'title' => 'Sukses!<br/>',
        'message' => 'Berhasil melakukan aski.'
    ];

    public function baca()
    {
        return view('product.fokusSpg');
    }

    public function data()
    {
        $product = ProductFokusSpg::with('product')->with('employee')
        ->select('product_fokus_spgs.*');
        return Datatables::of($product)
        ->addColumn('category_product', function($product) {
            $category = $product->product->subcategory->category->name;
            $product = $product->product->name;
            $category_product = $category.' - '.$product;
            return $category_product;
        })
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'category'      => $product->product->subcategory->category->id,
                'employee'      => $product->employee->id,
                'product'     	=> $product->product->id,
                'from'          => $product->from,
                'to'          	=> $product->to
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('fokusSpg.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (($validator = ProductFokusSpg::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($data['id_product'] as $key => $id_product){
            $from = explode('/', $data['from'][$key]);
            $data['from'][$key] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
            $to = explode('/', $data['to'][$key]);
            $data['to'][$key] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();
        }

        if (ProductFokusSpg::hasActivePF($data)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus SPG sudah ada!';
        } else {
            foreach ($data['id_product'] as $key => $id_product){
                ProductFokusSpg::create([
                'id_employee' => $data['id_employee'],
                'id_product' => $id_product,
                'from' => $data['from'][$key],
                'to' => $data['to'][$key],
                ]);

            }
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus SPG!';
        }

        return redirect()->back()->with($this->alert);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->all();
        $product = ProductFokusSpg::findOrFail($id);


        if (($validator = $product->validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        foreach ($data['id_product'] as $key => $id_product){
            $from = explode('/', $data['from'][$key]);
            $data['from'][$key] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
            $to = explode('/', $data['to'][$key]);
            $data['to'][$key] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();
        }

        // if (ProductFokusSpg::hasActivePF($data, $product->id)) {
        //     $this->alert['type'] = 'warning';
        //     $this->alert['title'] = 'Warning!<br/>';
        //     $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus SPG sudah ada!';
        // } else {

            // $productNot = ProductFokusSpg::where('id_employee',$data['id_employee'])->whereNotIn('id_product', $data['id_product'])->pluck('product_fokus_spgs.id');
            // foreach ($productNot as $produk_id) {
            //     ProductFokusSpg::where('id', $produk_id)->delete();
            // }

            foreach ($data['id_product'] as $key => $id_product){
                $pfSpg = ProductFokusSpg::where('id_employee', $data['id_employee'])->where('id_product',$id_product)->first();
                    $pfSpg->update([
                    'id_employee' => $data['id_employee'],
                    'id_product' => $id_product,
                    'from' => $data['from'][$key],
                    'to' => $data['to'][$key],
                    ]);
            }
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus SPG!';
        // }

        return redirect()->back()->with($this->alert);
    }

    public function delete($id)
    {
        $product = ProductFokusSpg::find($id);
            $product->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }

    public function export()
    {
 
        $fokus = ProductFokusSpg::orderBy('created_at', 'DESC')->get();
        $filename = "ProductfokusSpg_".Carbon::now().".xlsx";
        (new FastExcel($fokus))->download($filename, function ($fokus) {
            return [
                'Product'       => $fokus->product->name,
                'Month From'    => $fokus->from,
                'Month Until'   => $fokus->to
            ];
        });
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
                        $dataProduct['product_name']        = $row->product;
                        $dataProduct['product_code']        = $row->code;
                        $dataProduct['subcategory_name']    = $row->subcategory;
                        $dataProduct['category_name']       = $row->category;
                        $dataProduct['sku']                 = $row->sku;
                        $dataProduct['type']                = $row->type;
                        $dataProduct['value']               = $row->value;
                        $id_product = $this->findProduct($dataProduct);

                        // $data1 = Category::where(['id' => $id_product])->first();
                        // $check = Product::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->category))."'")
                        // ->where(['id_product' => $data1->id])->count();
                        // if ($check < 1) {
                            ProductFokusSpg::create([
                                'id_product'        => $id_product,
                                'from'              => Carbon::now(),
                                'to'                => Carbon::now()
                            ])->id;
                        // } else {
                        //     return false;
                        // }
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

    public function findProduct($data)
    {
        $dataProduct = Product::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['product_name']))."'");
        if ($dataProduct->count() < 1 ) {

            $data1['subcategory_name']= $data['subcategory_name'];
            $data1['category_name']   = $data['category_name'];
            $id_subcategory = $this->findSubcategory($data1);
            $getType = ProductStockType::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['type']))."'")->first()->id;
            $product = Product::create([
                'name'                => $data['product_name'],
                'code'                => $data['product_code'],
                'deskripsi'           => "-",
                'panel'               => "yes",
                'id_brand'            => 1,
                'stock_type_id'       => ($getType ? $getType : 1),
                'id_subcategory'      => $id_subcategory
          ]);
          $id_product = $product->id;
          if (!empty($product)) {
            $dataSku = array();
            $listSku = explode(",", $data['sku']);
            foreach ($listSku as $sku) {
                $dataSku[] = array(
                    'sku_unit_id'    		=> $this->findSku($sku, $data['value']),
                    'product_id'          	=> $product->id,
                );
            }
            DB::table('product_units')->insert($dataSku);
          }
        }else{
            $id_product = $dataProduct->first()->id;
        }
        return $id_product;
    }

    public function findSku($data, $value)
    {
        $dataSku = SkuUnit::get();
        if (!empty($dataSku)) {
            $sku = SkuUnit::create([
                'name'       	         => $data,
				'conversion_value'       => $value
            ]);
    
            if ($sku) {
                $id_sku = $sku->id;
            }
        } else {
            $id_sku = $dataSku->first()->id;
        }
        return $id_sku;
    }


    public function findSubcategory($data)
    {
        $dataSu = SubCategory::where('name','like','%'.trim($data['subcategory_name']).'%');
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
