<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Area;
use App\Channel;
use App\Product;
use App\JobTrace;
use App\ProductFocus;
use App\ProductFocusArea;
use App\Jobs\UploadFocus;
use App\Jobs\DownloadFocus;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\Datatables\Datatables;

class ProductFocusController extends Controller
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
        return view('product.product-focus', $data);
    }

    public function data()
    {
        $focus = ProductFocus::with(['product','productFocusArea'])
        ->orderBy('updated_at','desc')
        ->select('product_focus.*');
        return Datatables::of($focus)
        ->addColumn('area', function($focus) {
            if(!$focus->productFocusArea->isEmpty()){
                $area = "";
                $areas = [];
                foreach ($focus->productFocusArea as $key => $value) {
                    $areas[] = $value->area->name;
                }
				$area .= implode(', ', $areas);
			} else {
                $area = "ALL";
			}
			return $area;
		})
        ->addColumn('action', function ($focus) {
            $data = array(
                'id'            => $focus->id,
                'product'     	=> $focus->product()->select('id','name')->first(),
                'from'          => $focus->from,
                'to'            => $focus->to,
                'area'          => $focus->productFocusArea()
                    ->join('areas','areas.id','product_focus_areas.id_area')
                    ->select('id_product_focus','id_area','name')
                    ->get()
            );
            return "<button data-toggle='modal' data-target='#formModal' onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('fokusGTC.delete', $focus->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })
        ->rawColumns(['area','action'])
        ->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'product'   => 'required',
            'from'      => 'required|string',
            'to'        => 'required|string',
            'update'    => 'nullable|numeric',
            'id'        => 'nullable|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {

            $action = DB::transaction(function () use($data, $request) {
                $from   = Carbon::createFromFormat('d/m/Y','01/'.$request->from)->format('Y-m-d');
                $to     = Carbon::createFromFormat('d/m/Y','23/'.$request->to)->endOfMonth()->format('Y-m-d');
                $success = 'true';

                foreach ($request->product as $product) {
                    if($request->update == 1){
                        $pf = ProductFocus::whereId($request->id)->update([
                            'id_product'    => $product,
                            'from'          => $from,
                            'to'            => $to,
                            'deleted_at'    => null
                        ]);
                    }
                    if(empty($request->update)){
                        $pf = ProductFocus::withTrashed()->updateOrCreate([
                            'id_product'    => $product,
                            'from'          => $from,
                            'to'            => $to
                        ],[
                            'deleted_at'    => null
                        ]);
                    }

                    $area = [];
                    $pfId = !empty($pf->id) ? $pf->id : $request->id;
                    if (!empty($request->area)) {
                        $area = $request->area;
                        foreach ($request->area as $key => $value) {
                            ProductFocusArea::
                            updateOrCreate([
                                'id_product_focus'  => $pfId,
                                'id_area'           => $value,
                            ],[
                                'deleted_at' => null
                            ]);
                        }
                    }
                    ProductFocusArea::whereIdProductFocus($pfId)
                        ->whereNotIn('id_area', $area)
                        ->delete();
                }

                return $success;
            });

            if ($action == 'true') {
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil '.($request->update == 1 ? 'mengubah' : 'menambah').' Product Fokus!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'warning',
                    'title'  => 'Warning!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Gagal '.($request->update == 1 ? 'mengubah' : 'menambah').' Product Fokus!'
                ]);
            }
        }
    }

    public function delete($id)
    {
        $focus = ProductFocus::find($id);
        $focus->delete();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
    }

    public function export()
    {
 
        $fokus = ProductFocus::orderBy('id', 'DESC')->get();
        $filename = "Product_focus_".Carbon::now().".xlsx";
        (new FastExcel($fokus))->download($filename, function ($fokus) {
            return [
                'Product'       => $fokus->product->name,
                'Area'          => 
                    implode(', ',
                        $fokus->productFocusArea()
                        ->join('areas','areas.id','product_focus_areas.id_area')
                        ->pluck('areas.name')->toArray()
                    ),
                'Start Month'   => $fokus->from,
                'End Month'     => $fokus->to
            ];
        });
    }

    public function import(Request $request)
    {
        $file = Input::file('file')->getClientOriginalName();
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if ($extension != 'xlsx' && $extension !=  'xls') {
            return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
        }

        if($request->hasFile('file'))
        {
            $file   = $request->file('file')->getRealPath();
            $ext    = '';
            $index  = 1;
            $availPF = false;
            Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use($request, &$availPF, &$index) {
                if (!empty($results->all()))
                {
                    foreach($results as $row)
                    {
                        $index++;
                        $fokus = [];
                        $listProduct = explode(",", $row->sku);
                        foreach ($listProduct as $key => $product) {
                            $getSku = Product::whereRaw("TRIM(UPPER(code)) = '".trim(strtoupper($product))."'")->first();
                            if (isset($getSku->id))
                            {
                                $fokusTmplt = [
                                    'id_product' => $getSku->id,
                                    'from'       => Carbon::parse(\PHPExcel_Style_NumberFormat::toFormattedString($row['from'], 'MM/YYYY'))->toDateString(),
                                    'to'         => Carbon::parse(\PHPExcel_Style_NumberFormat::toFormattedString($row['until'], 'MM/YYYY'))->endOfMonth()->toDateString()
                                ];

                                $listArea = explode(",", $row->area);
                                if (empty($listArea)) {
                                    foreach ($listArea as $area) {
                                        $getArea = Area::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($area))."'")->first();
                                        if (isset($getArea->id)) {
                                            $fokus[] = array_merge($fokusTmplt, ["id_area"=>$getArea->id]);
                                        }else{

                                        }
                                    }
                                }else{
                                    $fokus[] = $fokusTmplt;
                                }
                            }else{
                                $index = $index .', Product Code \"'. $product .'\" tidak ditemukan.';
                                $fokus = [];
                                $availPF = true;
                                break;
                            }
                        }

                        DB::beginTransaction();
                        foreach ($fokus as $fokusData)
                        {
                            // validate if periode exists
                            $skipped = false;
                            if (isset(ProductFocus::where("id_product", $fokusData["id_product"])->where("from", $fokusData["from"])->where("to", $fokusData["to"])->first()->id))
                            {
                                $availPF = true;
                                $skipped = true;
                            }

                            if (!$skipped){
                                $PFGTCObj = ProductFocus::create($fokusData);
                            } 

                        }
                        DB::commit();
                    }
                } else {
                    throw new Exception("Error Processing Request", 1);
                }
            }, false);
            if ($availPF)
            {
                return redirect()->back()->with([
                    'type'      => 'danger',
                    'title'     => 'Gagal!<br/>',
                    'message'   => '<i class="em em-confounded mr-2"></i>Ada kegagalan tambah produk target. '.( (strlen($index) > 10) ? 'Cek baris ke '.$index : ' Silahkan cek data periode!' )
                ]);
            } else {
                return redirect()->back()->with([
                    'type' => 'success',
                    'title' => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk target!'
                ]);
            }
        } else {
            return redirect()->back()->with([
                'type' => 'danger',
                'title' => 'Gagal!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>File harus di isi!'
            ]);
        }
    }

    public function download()
    {
        $action = DB::transaction(function () {

            $directory  = 'export/master/product-focus';
            $now        = Carbon::now();
            $filecode   = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);


            try{

                // TRACING AND QUEUE
                $trace = JobTrace::create([
                        'id_user'   => Auth::user()->id,
                        'title'     => "Export Product Focus ".$filecode,
                        'date'      => $now,
                        'model'     => 'App\ProductFocus',
                        'directory' => $directory,
                        'type'      => 'D',
                        'status'    => 'PROCESSING',
                    ]);

                dispatch(new DownloadFocus($trace));
                return true;
            }catch(\Exception $e){
                DB::rollback();
                return $e;
            }

        });

        if ($action)
        {
            return redirect()->back()->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil Download File, Silahkan tunggu sejenak!'
            ]);
        } else {
            return redirect()->back()->with([
                'type'      => 'danger',
                'title'     => 'Gagal!<br/>',
                'message'   => '<i class="em em-confounded mr-2"></i>Gagal Download File, hubungi admin!'
            ]);
        }
    }

    public function upload(Request $request)
    {
        $action = DB::transaction(function () use ($request) {

            $directory  = 'imports/product-focus';
            $now        = Carbon::now();
            $path       = public_path($directory);
            $file       = $request->file('file');
            $name       = "(@".$now->format('Ymdhis').") " . $file->getClientOriginalName();

            try{

                // SAVE EXCEL
                $file->move($path, $name);

                // TRACING AND QUEUE
                $trace = JobTrace::create([
                        'id_user'   => Auth::user()->id,
                        'title'     => "Import Product Focus ".$now,
                        'date'      => $now,
                        'model'     => 'App\ProductFocus',
                        'directory' => $directory,
                        'type'      => 'U',
                        'file_path' => $path,
                        'file_name' => $name,
                        'status'    => 'PROCESSING',
                    ]);

                dispatch(new UploadFocus($trace));
                return true;
            }catch(\Exception $e){
                DB::rollback();
                return $e;
            }

        });

        if ($action)
        {
            return redirect()->back()->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil Upload File, Silahkan tunggu sejenak!'
            ]);
        } else {
            return redirect()->back()->with([
                'type'      => 'danger',
                'title'     => 'Gagal!<br/>',
                'message'   => '<i class="em em-confounded mr-2"></i>Gagal Upload File, hubungi admin!'
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
