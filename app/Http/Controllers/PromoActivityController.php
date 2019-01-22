<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use Excel;
use Yajra\Datatables\Datatables;
use Auth;
use App\EmployeeStore;
use App\Product;
use App\Employee;
use App\Store;
use App\Promo;
use App\PromoDetail;
use App\Brand;
use DB;

class PromoActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('report.promoActivity');        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    // public function importXLS(Request $request)
    // {
    //             $this->validate($request, [
    //         'file' => 'required'
    //     ]);

    //     $transaction = DB::transaction(function () use ($request) {
    //         $file = Input::file('file')->getClientOriginalName();
    //         $filename = pathinfo($file, PATHINFO_FILENAME);
    //         $extension = pathinfo($file, PATHINFO_EXTENSION);

    //         if ($extension != 'xlsx' && $extension !=  'xls') {
    //             return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
    //         }
    //         if($request->hasFile('file')){
    //             $file = $request->file('file')->getRealPath();
    //             $ext = '';

    //             Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results)
    //                 {
    //                     foreach($results as $row)
    //                     {

    //                      Promo::create([
    //                         'id_employee' => $row->id_employee,
    //                         'id_store' => $row->id_store,
    //                         'id_brand' => $row->id_brand,
    //                         'date' => $row->date,
    //                     ]);

    //                      PromoDetail::create([
    //                         'id_promo' => $row->id_promo,
    //                         'id_product' => $row->id_product,
    //                         'type' => $row->type,
    //                         'description' => $row->description,
    //                         'start_date' => $row->start_date,
    //                         'end_date' => $row->end_date,
    //                         'id_product' => $row->id_product,
    //                      ]);
    //                     }
    //                 },false);
    //         }
    //         return 'success';
    //     });

    //     if ($transaction == 'success') {
    //         return redirect()->back()
    //             ->with([
    //                 'type'      => 'success',
    //                 'title'     => 'Sukses!<br/>',
    //                 'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil import!'
    //             ]);
    //     }else{
    //         return redirect()->back()
    //             ->with([
    //                 'type'    => 'danger',
    //                 'title'   => 'Gagal!<br/>',
    //                 'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
    //             ]);
    //     }
    // }

    public function data(Request $request)
    {
           // $pk = DB::table('promo_details')
           //  ->join('promos', 'promo_details.id_promo', '=', 'promos.id')
           //  ->join('products', 'promo_details.id_product', '=', 'products.id')
           //  ->join('stores', 'promos.id_store', '=', 'stores.id')
           //  ->join('employees', 'promos.id_employee', '=', 'employees.id')
           //  ->join('brands', 'promos.id_brand', '=', 'brands.id')
           //    ->select('promo_details.*', 'employees.name', 'stores.name1', 'brands.name', 'products.name');
        $promoDetail = PromoDetail::with(['promo', 'product'])->orderBy('id','desc')
        ->when($request->has('product'), function ($q) use ($request){
            return $q->where('id_product',$request->input('product'));
        })
        ->when($request->has('product_competitor'), function ($q) use ($request){
            return $q->where('id_product_competitor',$request->input('product_competitor'));
        })
        ->when($request->has('employee'), function ($q) use ($request){
            return $q->whereHas('promo', function($q2) use ($request)
            {
                return $q2->where('id_employee',$request->input('employee'));
            });
        })
        ->when($request->has('begin_periode'), function ($q) use ($request){
            return $q->whereDate('start_date', '>=', "'".Carbon::parse('1/'.$request->input('begin_periode'))->format('Y-m-d')."'");
        })
        ->when($request->has('end_periode'), function ($q) use ($request){
            return $q->whereDate('end_date', '<=', "'".Carbon::parse('1/'.$request->input('end_periode'))->endOfMonth()->format('Y-m-d')."'");
        })
        ->when(!empty($request->input('store')), function ($q) use ($request){
            return $q->whereHas('promo', function($q2) use ($request)
            {
                return $q2->where('id_store', $request->input('store'));
            });
        })
        ->when($request->has('area'), function ($q) use ($request){
            return $q->whereHas('promo', function($q2) use ($request)
            {
                return $q2->whereHas('store', function($q3) use ($request)
                {
                    return $q3->whereHas('subarea', function($q4) use ($request)
                    {
                        return $q4->where('id_area', $request->input('area'));
                    });
                });
            });
        })
        ->select('promo_details.*');
        return Datatables::of($promoDetail)
        
        ->addColumn('action', function ($promoDetail) {
            return "<a href=".route('ubah.pa', $promoDetail->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('pa.delete', $promoDetail->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })
        ->addColumn('images', function($promoDetail) {

            return "
            <a href=".asset('uploads/promo/'.$promoDetail->promo->image1)." class='btn btn-sm btn-info btn-square popup-image'><i class='si si-picture' ></i> Product 1</a> 
            <a href=".asset('uploads/promo/'.$promoDetail->promo->image2)." class='btn btn-sm btn-info btn-square popup-image'><i class='si si-picture'></i> Product 2</a> 
            <a href=".asset('uploads/promo/'.$promoDetail->promo->image3)." class='btn btn-sm btn-info btn-square popup-image'><i class='si si-picture'></i> Product 3</a>";
        })
        ->addColumn('store', function($promoDetail) {
            return $promoDetail->promo->store->name1;
        })
        ->addColumn('employee', function($promoDetail) {
            return $promoDetail->promo->employee->name;
        })
        ->addColumn('product', function($promoDetail) {
            if (!empty($promoDetail->id_product)) {
                return $promoDetail->product->name;
            }else{
                return $promoDetail->productCompetitor->name;
            }
        })
        ->addColumn('brand', function($promoDetail) {
            if (!empty($promoDetail->id_product)) {
                return $promoDetail->product->brand->name;
            }else{
                return $promoDetail->productCompetitor->brand->name;
            }
        })
        ->rawColumns(['images', 'action'])
        ->make(true);
    }


    public function exportXLS()
    {

     Excel::create('Report_Activity_'.Carbon::now(), function($excel){

        $excel->sheet('Report Activity', function($sheet){
            $sheet->cells('A1:I1', function($cells) {
                $cells->setFontWeight('bold');
                $cells->setAlignment('left');
                $cells->setBackground('#74fd84');
            });

            $sheet->row(1, ['Name Store', 'Name Employee', 'Name Brands', 'Name Product', 'Type', 'Description Promo', 'Start Promo', 'End Promo', 'Images']);
            $oke=PromoDetail::orderBy('id','DESC')->get();

            foreach ($oke as $ok) {
                $sheet->appendRow([
                    $ok->promo->store->name1, 
                    $ok->promo->employee->name,
                    $ok->promo->brand->name,
                    (!empty($ok->id_product)?$ok->product->name:$ok->productCompetitor->name),
                    $ok->type,
                    $ok->description,
                    $ok->start_date,
                    $ok->end_date,
                ]);
            }
        });
    })->export('xlsx');
     return ;
 }
 public function update(Request $request, $id)
 {
        //
 }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
