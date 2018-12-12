<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use File;
use Excel;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\ProductKnowledge;
use App\Position;
use DB;
class CashAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('report.democooking.cash');
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
                Excel::filter('chunk')->selectSheetsByIndex(1)->load($file)->chunk(250, function(&$results)
                {
                    
                    // foreach($results as $row)
                    // {
                    //     echo "$row->keterangan<hr>";
                    //     // $dataProduct['subcategory_name']    = $row->subcategory;
                    //     // $dataProduct['category_name']       = $row->category;
                    //     // $id_subcategory = $this->findSub($dataProduct);

                    //     // $data1 = SubCategory::where(['id' => $id_subcategory])->first();
                    //     // $check = Product::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->sku))."'")
                    //     // ->where(['id_subcategory' => $data1->id])->count();
                    //     // if ($check < 1) {
                    //     //     $getType = ProductStockType::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->type))."'")->first()->id;
                    //     //     $insert = Product::create([
                    //     //         'id_brand'          => 1,
                    //     //         'id_subcategory'    => $id_subcategory,
                    //     //         'code'              => $row->code,
                    //     //         'name'              => $row->sku,
                    //     //         'carton'            => (isset($row->carton) ? $row->carton : "-"),
                    //     //         'pack'              => (isset($row->pack) ? $row->pack : "1"),
                    //     //         'pcs'               => 1,
                    //     //         'stock_type_id'     => ($getType ? $getType : 1),
                    //     //         'panel'             => ($row->panel ? $row->panel : "yes")
                    //     //     ]);
                    //     // } else {
                    //     //     return false;
                    //     // }
                    // }
                },false);
                return response()->json($results->toArray());
            }
            return 'success';
        });

        // if ($transaction == 'success') {
        //     return redirect()->back()
        //     ->with([
        //         'type'      => 'success',
        //         'title'     => 'Sukses!<br/>',
        //         'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil import!'
        //     ]);
        // }else{
        //     return redirect()->back()
        //     ->with([
        //         'type'    => 'danger',
        //         'title'   => 'Gagal!<br/>',
        //         'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
        //     ]);
        // }
    }
}
