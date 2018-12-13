<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductFokusGtc;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use App\Area;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\Datatables\Datatables;

class ProductFokusGtcController extends Controller
{
    private $alert = [
        'type' => 'success',
        'title' => 'Sukses!<br/>',
        'message' => 'Berhasil melakukan aski.'
    ];

    public function baca()
    {
        $data['product']  = Product::get();
        $data['area']   = Area::get();
        return view('product.productGtc', $data);
    }

    public function data()
    {
        $product = ProductFokusGtc::with(['product', 'area'])
        ->select('product_fokus_gtcs.*');
        return Datatables::of($product)
        ->addColumn('area', function($product) {
			if (isset($product->area)) {
				$area = $product->area->name;
			} else {
				$area = "ALL";
			}
			return $area;
		})
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'product'     	=> (isset($product->product->id) ? $product->product->id : ""),
                'area'     	    => (isset($product->area->id) ? $product->area->id : ""),
                'from'          => $product->from,
                'to'          	=> $product->to
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('fokusGTC.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $data=$request->all();
        $limit=[
            'from'        => 'required',
            'product'     => 'required',
            'to'          => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            if ($request->input('from')) {
                $from = Carbon::createFromFormat('d/m/Y','01/'.$request->input('from'))->startOfMonth()->format('Y-m-d');
            } else {
                $from = null;
            }
            if ($request->input('to')) {
                $to = Carbon::createFromFormat('d/m/Y','23/'.$request->input('to'))->endOfMonth()->format('Y-m-d');
            } else {
                $to = null;
            }

            $checkProduct = Product::whereIn('id', $request->input('product'))->first();
            $checkArea = (isset(Area::where('id', $request->input('area'))->first()->id) ? Area::where('id',$request->input('area'))->first()->id : null);
            $checkAll = ProductFokusGtc::where(function($query) use ($from, $to){
                $query->whereBetween('from', [$from, $to]);
                $query->orWhereBetween('to', [$to, $from]);
            })
            // ->where(function($query) use ($checkProduct, $checkArea){
            //     $query->whereIn('id_product', [$checkProduct->id, (isset($checkArea->id) ? $$checkArea->id : null)]);
            //     $query->whereIn('id_area', [(isset($checkArea->id) ? $$checkArea->id : null), $checkProduct->id]);
            // })
            ->where(['id_product' => $checkProduct->id])
            ->where(['id_area' => (isset($checkArea->id) ? $checkArea->id : null)])
            ->count();
            // dd($checkAll);
            if ($checkAll < 1){
                foreach ($request->input('product') as $product) {
                    ProductFokusGtc::create([
                            'id_product' 	=> $product,
                            'id_area' 	    => $request->input('area') ? $request->input('area') : null,
                            'from' 			=> $from,
                            'to' 			=> $to
                    ]);
                }
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Product Fokus GTC!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'warning',
                    'title'  => 'Warning!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Product Fokus GTC sudah ada!'
                ]);
            }
        }
    }

    public function update(Request $request, $id) 
    {
        $data = $request->all();
        $product = ProductFokusGtc::findOrFail($id);

        if (($validator = $product->validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $from = explode('/', $data['from']); 
        $to = explode('/', $data['to']);
        $data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
        $data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

        if (ProductFokusGtc::hasActivePF($data, $product->id)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus GTC sudah ada!';
        } else {
            $product->fill($data)->save();
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus GTC!';
        }

        return redirect()->back()->with($this->alert);
    }

    public function delete($id)
    {
        $product = ProductFokusGtc::find($id);
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
 
        $fokus = ProductFokusGtc::orderBy('created_at', 'DESC')->get();
        $filename = "ProductfokusGTC_".Carbon::now().".xlsx";
        (new FastExcel($fokus))->download($filename, function ($fokus) {
            return [
                'Product'       => $fokus->product->name,
                'Area'          => (isset($fokus->area->name) ? $fokus->area->name : ""),
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
                            ProductFokusMD::create([
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
