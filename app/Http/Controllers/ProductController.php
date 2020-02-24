<?php

namespace App\Http\Controllers;
use File;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Filters\ProductFilters;
use App\ProductPrice;
use App\Product;
use App\ProductFocus;
use App\ProductUnit;
use App\ProductPromo;
use App\ProductMeasure;
use App\SubCategory;
use App\Category;
use App\Brand;
use App\ProductStockType;
use App\SkuUnit;
use App\Traits\StringTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Helper\ExcelHelper as ExcelHelper;

class ProductController extends Controller
{
    use StringTrait;

    protected $excelHelper;

    public function __construct(ExcelHelper $excelHelper)
    {
        $this->excelHelper = $excelHelper;
    }

    public function getDataWithFilters(ProductFilters $filters){
        $data = Product::filter($filters)->limit(10)->get();
        return $data;
    }


    public function getProductByCategory($param){

        $data = Product::join('sub_categories','products.id_sub_category','sub_categories.id')
                        ->join('categories','sub_categories.id_category', 'categories.id')
                        ->where('categories.id',$param)
                        ->select('products.*')->get();
        return $data;
    }

   public function baca()
   {
       return view('product.product');
   }

   public function data()
    {
        $product = Product::with('subCategory')->with('sku_units')
        ->whereNull('deleted_at')
        ->orderBy('updated_at', 'desc')
        ->select('products.*');
        return Datatables::of($product)
        ->editColumn('panel', function($product) {
            return ucfirst($product->panel);
        })
        ->addColumn('brand', function($product) {
            return $product->brand->name;
        })
        ->addColumn('category', function($product) {
            return $product->subcategory->category->name;
        })
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'product'       => $product->id_product,
                'name'          => $product->name,
                'code'          => $product->code,
                'deskrispi'     => $product->deskripsi,
                'panel'         => $product->panel,
                'carton'        => $product->carton,
                'pack'          => $product->pack,
                'pcs'           => $product->pcs,
                'id_sub_category'   => $product->id_sub_category,
                'sub_category_name' => $product->subCategory->name,
               
            );
            return "<button onclick='editModalProduct(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('product.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $limit=[
            'name'              => 'required|string',
            'code'              => 'required|string',
            'panel'             => 'nullable|string',
            'sub_category'      => 'nullable|string',
            'category'          => 'nullable|string',
            'brand'             => 'nullable|string',
            'new_sub_category'  => 'nullable|string',
            'new_category'      => 'nullable|string',
            'new_brand'         => 'nullable|string',
            'carton'            => 'nullable|numeric',
            'pack'              => 'nullable|numeric',
            'pcs'               => 'nullable|numeric',
            'update'            => 'nullable|numeric',
        ];

        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $actionType = ($request->update == 1 ? 'mengubah' : 'menambah');


        $data = $request->only(['name', 'code', 'panel', 'pcs', 'pack', 'carton']);

        $action = DB::transaction(function () use($data, $request) {
            // $measure = $data['measure'];
            // unset($data['measure']);
            $success = 'true';

            if (!$request->has('sub_category')) {
                
                if (!empty($request->new_sub_category)) {

                    if (!$request->has('category')) {
                    
                        if (!$request->has('brand')) {
                            if (!empty($request->new_brand)) {
                                $brand = Brand::firstOrCreate([ 'name' => $this->trimAndUpper($request->new_brand) ]);
                            }else{
                                $success = 'Choose or input the Brand.';
                            }
                        }else{
                            $brand = Brand::where([ 'id' => $this->getFirstExplode($request->brand, '`^') ])->first();
                        }

                        if (!empty($request->new_category)) {
                            $category = Category::firstOrCreate([ 'name' => $this->trimAndUpper($request->new_category), 'id_brand' => $brand->id ]);
                        }else{
                            $success = 'Choose or input the Category.';
                        }
                    }else{
                        $category = Category::where([ 'id' => $this->getFirstExplode($request->category, '`^') ])->first();
                    }

                    if (!empty($request->new_sub_category) && $success == 'true') {
                        $subCategory = SubCategory::firstOrCreate([ 'name' => $this->trimAndUpper($request->new_sub_category), 'id_category' => $category->id ]);
                    }else{
                        $success = 'Choose or input the Sub Category.';
                    }
                }else{
                    $success = 'Choose the Sub Category.';
                }
            }else{
                $subCategory = SubCategory::where([ 'id' => $this->getFirstExplode($request->sub_category, '`^') ])->first();
            }

            if ($success == 'true') {
                $data['id_sub_category'] = $subCategory->id;

                if ($request->update == 1) {
                    $product = Product::whereId($request->id)
                        ->update($data);
                }else{
                    $product = Product::create($data);
                }
                // foreach ($measure as $sku_id) {
                //     ProductMeasure::create([
                //         'id_product' => $product->id,
                //         'id_measure' => $sku_id
                //     ]);
                // }
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
        $product = Product::find($id);

        $prc    = ProductPrice::where(['id_product' => $product->id])->count();
        $pf     = ProductFocus::where(['id_product' => $product->id])->count();
        $jumlah = $prc + $pf;

        if (!$jumlah < 1) 
        {
            $result = [
                'status'    => false,
                'type'      => 'danger',
                'title'     => 'Gagal!<br/>',
                'message'   => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain di Product Price dan Product Focus!'
            ];
        } else {
            $product->delete();

            $result = [
                'status'    => true,
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
           ];
        }

        return response()->json($result, 200);
    }

    public function export(ProductFilters $filters)
    {
        $product    = Product::filter($filters)->orderBy('id', 'DESC')->get();
        $filecode   = $this->createFileCode();
        $fileName   = $this->setFileName("Product_(".$filecode.")");
        $filePath   = 'export/master/product';
        $data       = [];

        foreach ($product as $idx => $val) {
            $data[$idx]['brand']        = @$val->brand->name;
            $data[$idx]['category']     = @$val->category->name;
            $data[$idx]['sub_category'] = @$val->subCategory->name;
            $data[$idx]['code']         = @$val->code;
            $data[$idx]['name']         = @$val->name;
            $data[$idx]['panel']        = ucfirst(@$val->panel);
            $data[$idx]['carton']       = @$val->carton;
            $data[$idx]['pack']         = @$val->pack;
            $data[$idx]['pcs']          = @$val->pcs;
        }

        $excel = Excel::create($fileName, function($excel) use ($data) {

            $excel->setTitle('Master - Product');

            $excel->setCreator('TNS')
                  ->setCompany('TNS');

            $excel->setDescription('Product Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Product', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:I1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForProduct($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:I1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:I1', 'thin');
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
        $this->validate($request, [
            'file' =>   'required'
        ]);

        $transaction = DB::transaction(function () use ($request) {

            $file       = Input::file('file')->getClientOriginalName();
            $filename   = pathinfo($file, PATHINFO_FILENAME);
            $extension  = pathinfo($file, PATHINFO_EXTENSION);

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
                        $dataProduct['sub_category_name']   = $row->sub_category;
                        $dataProduct['category_name']       = $row->category;
                        $dataProduct['brand_name']          = $row->brand;
                        $id_sub_category = $this->findSub($dataProduct);

                        $data1 = SubCategory::where(['id' => $id_sub_category])->first();
                        $check = Product::whereRaw("TRIM(UPPER(code)) = '". trim(strtoupper($row->code))."'")
                            ->where(['id_sub_category' => $data1->id])->count();
                        if ($check == 0) {
                            $insert = Product::updateOrCreate([
                                'id_sub_category'   => $id_sub_category,
                                'code'              => $row->code,
                                'name'              => $row->name,
                                'panel'             => ($row->panel ? $row->panel : "yes"),
                                'carton'            => (isset($row->carton) ? $row->carton : null),
                                'pack'              => (isset($row->pack) ? $row->pack : null),
                                'pcs'               => 1,
                            ]);
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

    public function download(Request $request)
    {
        $action = DB::transaction(function () use ($request){
            $directory  = 'export/master/product';
            $now        = Carbon::now();
            $filecode   = $this->createFileCode();
            $filterText = $this->getFilterText($request->all());
            $title      =  "Export Product" . " (" . $filecode . ")" . $filterText;

            try{
                $trace = JobTrace::create([
                        'id_user'   => Auth::user()->id,
                        'title'     => $title,
                        'date'      => $now,
                        'model'     => 'App\Product',
                        'results'   => asset($directory),
                        'file_path' => $directory,
                        'file_name' => $this->setFileName($title),
                        'type'      => 'D',
                        'status'    => 'PROCESSING',
                    ]);

                dispatch(new DownloadProduct($trace, $request->all()));
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

            $directory  = 'imports/product';
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
                        'title'     => "Import Product ".$now,
                        'date'      => $now,
                        'model'     => 'App\Product',
                        'type'      => 'U',
                        'results'   => asset($directory),
                        'file_path' => $directory,
                        'file_name' => $name,
                        'status'    => 'PROCESSING',
                    ]);

                dispatch(new UploadProduct($trace));
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

    public function findSub($data)
    {
        $dataCategory['category_name']  = $data['category_name'];
        $dataCategory['brand_name']     = $data['brand_name'];
        $id_category    = $this->findCategory($dataCategory);
        $dataSu         = SubCategory::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['sub_category_name']))."'")->whereIdCategory($id_category);

        if ($dataSu->count() == 0) {
            $sub = SubCategory::create([
              'name'            => $data['sub_category_name'],
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
        $dataBrand['brand_name']  = $data['brand_name'];
        $id_brand       = $this->findBrand($dataBrand);
        $dataCategory   = Category::where('name','like','%'.trim($data['category_name']).'%')->whereIdBrand($id_brand);

        if ($dataCategory->count() == 0) {
            $category = Category::create([
              'id_brand'    => $id_brand,
              'name'        => $data['category_name'],
            ]);
            $id_category = $category->id;
        }else{
            $id_category = $dataCategory->first()->id;
        }

        return $id_category;
    }

    public function findBrand($data)
    {
        $dataBrand = Brand::where('name','like','%'.trim($data['brand_name']).'%');

        if ($dataBrand->count() == 0) {
            $brand = Brand::create([
              'name'           => $data['brand_name'],
              'description'    => "-"
            ]);
            $id_brand = $brand->id;
        }else{
            $id_brand = $dataBrand->first()->id;
        }

        return $id_brand;
    }
}
