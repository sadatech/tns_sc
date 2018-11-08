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

    public function importXLS(Request $request)
    {
                $this->validate($request, [
            'file' => 'required'
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
                         
                         Promo::create([
                            'id_employee' => $row->id_employee,
                            'id_store' => $row->id_store,
                            'id_brand' => $row->id_brand,
                            'date' => $row->date,
                        ]);

                         PromoDetail::create([
                            'id_promo' => $row->id_promo,
                            'id_product' => $row->id_product,
                            'type' => $row->type,
                            'description' => $row->description,
                            'start_date' => $row->start_date,
                            'end_date' => $row->end_date,
                            'id_product' => $row->id_product,
                         ]);
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

    public function data()
    {
           // $pk = DB::table('promo_details')
           //  ->join('promos', 'promo_details.id_promo', '=', 'promos.id')
           //  ->join('products', 'promo_details.id_product', '=', 'products.id')
           //  ->join('stores', 'promos.id_store', '=', 'stores.id')
           //  ->join('employees', 'promos.id_employee', '=', 'employees.id')
           //  ->join('brands', 'promos.id_brand', '=', 'brands.id')
           //    ->select('promo_details.*', 'employees.name', 'stores.name1', 'brands.name', 'products.name');
        $promoDetail = PromoDetail::with(['promo', 'product'])
        ->select('promo_details.*');
        return Datatables::of($promoDetail)
        
        ->addColumn('action', function ($promoDetail) {
            return "<a href=".route('ubah.pk', $promoDetail->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('pk.delete', $promoDetail->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })
        ->addColumn('images', function($promoDetail) {

            return "
            <a href=".asset('uploads/promo/'.$promoDetail->promo->image1)." class='btn btn-sm btn-info btn-square popup-image'><i class='si si-picture mr-2'></i> Image Product</a>";
        })
        ->addColumn('store', function($promoDetail) {
        return $promoDetail->promo->store->name1;
        })
        ->addColumn('employee', function($promoDetail) {
        return $promoDetail->promo->employee->name;
        })
        ->addColumn('product', function($promoDetail) {
        return $promoDetail->product->name;
        })
        ->addColumn('brand', function($promoDetail) {
        return $promoDetail->promo->brand->name;
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
                $cells->setAlignment('center');
            });

            $sheet->row(1, ['Name Store', 'Name Employee', 'Name Brands', 'Name Product', 'Type', 'Description Promo', 'Start Promo', 'End Promo', 'Images']);
            $oke=PromoDetail::orderBy('id','DESC')->get();

            foreach ($oke as $ok) {
                $sheet->appendRow([
                    $ok->id_promo, 
                    $ok->promo->store->name1, 
                    $ok->promo->employee->name,
                    $ok->promo->brand->name,
                    $ok->product->name,
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
