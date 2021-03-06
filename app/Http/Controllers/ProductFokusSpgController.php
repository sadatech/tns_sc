<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Category;
use App\SubCategory;
use App\Employee;
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
use Illuminate\Support\Collection;

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

        foreach ($data['id_product'] as $key => $id_product){
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus SPG!';
            if ($id_product == "all")
            {
                foreach(Product::get() as $Product)
                {
                    if (ProductFokusSpg::hasActivePF([
                        'id_employee' => $data['id_employee'],
                        'id_product' => [$Product->id],
                        'from' => [$data['from'][$key]],
                        'to' => [$data['to'][$key]],
                        ]))
                    {
                        $this->alert['type'] = 'warning';
                        $this->alert['title'] = 'Warning!<br/>';
                        $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus SPG sudah ada!';
                    } else {
                        ProductFokusSpg::create([
                        'id_employee' => $data['id_employee'],
                        'id_product' => $Product->id,
                        'from' => $data['from'][$key],
                        'to' => $data['to'][$key],
                        ]);
                    }
                }
            } else {
                if (ProductFokusSpg::hasActivePF([
                    'id_employee' => $data['id_employee'],
                    'id_product' => [$id_product],
                    'from' => [$data['from'][$key]],
                    'to' => [$data['to'][$key]],
                    ]))
                {
                    $this->alert['type'] = 'warning';
                    $this->alert['title'] = 'Warning!<br/>';
                    $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus SPG sudah ada!';
                } else {
                    ProductFokusSpg::create([
                    'id_employee' => $data['id_employee'],
                    'id_product' => $id_product,
                    'from' => $data['from'][$key],
                    'to' => $data['to'][$key],
                    ]);
                }
            }
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

        $file = Input::file('file')->getClientOriginalName();
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if ($extension != 'xlsx' && $extension !=  'xls') {
            return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
        }

        if($request->hasFile('file'))
        {
            $file = $request->file('file')->getRealPath();
            $ext = '';
            Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use($request) {
                if (!empty($results->all()))
                {
                    $newDatas = new Collection();
                    foreach($results as $row)
                    {
                        $getEmp = Employee::whereRaw("TRIM(UPPER(nik)) = '".trim(strtoupper($row['nik']))."'")->first();
                        $getSku = Product::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($row['sku']))."'")->first();
                        if (isset($getEmp->id))
                        {
                            if (isset($getSku->id))
                            {
                                    $temp['id_employee']    = $getEmp->id;
                                    $temp['id_product']     = $getSku->id;
                                    $temp['from']           = Carbon::parse(\PHPExcel_Style_NumberFormat::toFormattedString($row['from'], 'YYYY-MM'))->startOfMonth()->toDateString();
                                    $temp['to']             = Carbon::parse(\PHPExcel_Style_NumberFormat::toFormattedString($row['until'], 'YYYY-MM'))->endOfMonth()->toDateString();

                            $newDatas->push($temp);

                            }else{
                                return redirect()->back()->with([
                                    'type' => 'danger',
                                    'title' => 'Gagal!<br/>',
                                    'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah fokus spg, tidak ditemukan produk '.$row["sku"].' Silahkan cek data kembali!'
                                ]);
                            }
                        }else{
                            return redirect()->back()->with([
                                'type' => 'danger',
                                'title' => 'Gagal!<br/>',
                                'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah fokus spg, tidak ditemukan nik '.$row["nik"].' Silahkan cek data kembali!'
                            ]);
                        }
                    }


                    foreach ($newDatas as $newData)
                    {
                        if (isset(ProductFokusSpg::where("id_employee", $newData["id_employee"])->where("id_product", $newData["id_product"])->where("from", $newData["from"])->where("to", $newData["to"])->first()->id))
                        {
                            $skipPF = true;
                        }

                        if (!isset($skipPF)){
                            ProductFokusSpg::create([
                                'id_employee'        => $newData["id_employee"],
                                'id_product'        => $newData["id_product"],
                                'from'              => $newData["from"],
                                'to'                => $newData["to"]
                            ]);
                        }
                    }
                } else {
                    throw new Exception("Error Processing Request", 1);
                }
            }, false);
            return redirect()->back()->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah fokus spg!'
            ]);
        } else {
            return redirect()->back()->with([
                'type' => 'danger',
                'title' => 'Gagal!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>File harus di isi!'
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
