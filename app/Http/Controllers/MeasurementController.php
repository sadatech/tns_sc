<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Area;
use App\Channel;
use App\Product;
use App\JobTrace;
use App\ProductFocus;
use App\ProductFocusArea;
use App\Traits\StringTrait;
use App\Traits\UploadTrait;
use App\Jobs\UploadFocus;
use App\Jobs\DownloadFocus;
use App\Filters\ProductFocusFilters;
use DB;
use Auth;
use File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Rap2hpoutre\FastExcel\FastExcel;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\ExcelHelper as ExcelHelper;

class MeasurementController extends Controller
{

    use StringTrait, UploadTrait;

    protected $excelHelper;

    public function __construct(ExcelHelper $excelHelper)
    {
        $this->excelHelper = $excelHelper;
    }

    public function baca()
    {
        return view('product.measurement');
    }

    public function data()
    {
        $data = MeasurementUnit::orderBy('updated_at','desc');
        ->select('product_focus.*');
        return Datatables::of($data)
        ->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name' => 'required|string',
            'size' => 'required|numeric',
        ];

        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $actionType = ($request->update == 1 ? 'mengubah' : 'menambah');

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
            $result = [
                'status'  => true,
                'type'    => 'success',
                'title'   => 'Sukses!<br/>',
                'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil '.$actionType.' Product Fokus!'
            ];
        } else {
            $result = [
                'status' => false,
                'type'   => 'warning',
                'title'  => 'Warning!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Gagal '.$actionType.' Product Fokus!'
            ];
        }

        return response()->json($result, 200);
    }

    public function delete($id)
    {
        $focus = ProductFocus::find($id);
        $focus->delete();

        $result = [
            'status'    => true,
            'type'      => 'success',
            'title'     => 'Sukses!',
            'message'   => 'Berhasil Delete Data!',
        ];

        return response()->json($result, 200);
    }

    public function export(ProductFocusFilters $filters)
    {
 
        $focus      = ProductFocus::filter($filters)->orderBy('id', 'DESC')->get();
        $filecode   = $this->createFileCode();
        $fileName   = $this->setFileName("Product_focus_(".$filecode.")");
        $filePath   = 'export/master/product-focus';

        foreach ($focus as $index => $fc) {
            $area = @$fc->productFocusArea()
                ->join('areas','areas.id','product_focus_areas.id_area')
                ->pluck('name')->toArray();

            $data[$index]['product']        = @$fc->product->name;
            $data[$index]['area']           = count($area) > 0 ? implode(', ', $area) : 'ALL';
            $data[$index]['start_month']    = $fc->from;
            $data[$index]['end_month']      = $fc->to;
        }

        $excel = Excel::create($fileName, function($excel) use ($data) {

            $excel->setTitle('Master - Product Focus');

            $excel->setCreator('TNS')
                  ->setCompany('TNS');

            $excel->setDescription('Product Focus Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Focus', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });

        })->store("xlsx", public_path($filePath), true);

        $result = [
            'status'    => true,
            'type'      => 'success',
            'title'     => 'Sukses!',
            'message'   => 'Berhasil Request Download!',
            'file'      => '../'.$filePath.'/'.$fileName.'.xlsx'
        ];

        return response()->json($result, 200);
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

    public function download(Request $request)
    {
        $action = DB::transaction(function () use ($request){
            $directory  = 'export/master/product-focus';
            $now        = Carbon::now();
            $filecode   = $this->createFileCode();
            $filterText = $this->getFilterText($request->all());
            $title      =  "Export Product Focus " . " (" . $filecode . ")" . $filterText;

            try{

                $trace = JobTrace::create([
                        'id_user'   => Auth::user()->id,
                        'title'     => $title,
                        'date'      => $now,
                        'model'     => 'App\ProductFocus',
                        'results'   => asset($directory),
                        'file_path' => $directory,
                        'file_name' => $this->setFileName($title),
                        'type'      => 'D',
                        'status'    => 'PROCESSING',
                    ]);

                dispatch(new DownloadFocus($trace, $request->all()));
                return true;
            }catch(\Exception $e){
                DB::rollback();
                return $e;
            }

        });

        $result = [
            'status'    => true,
            'type'      => 'success',
            'title'     => 'Sukses!',
            'message'   => 'Berhasil Request Download, Silahkan periksa VIEW JOB STATUS!'
        ];

        return response()->json($result, 200);
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
                        'type'      => 'U',
                        'results'   => asset($directory),
                        'file_path' => $directory,
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
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil Upload File, Silahkan periksa di VIEW JOB STATUS!'
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
