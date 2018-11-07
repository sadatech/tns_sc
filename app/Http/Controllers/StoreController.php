<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Auth;
use App\Area;
use DB;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use Box\Spout\Writer\Style\Color;
use File;
use Excel;
use App\Store;
use App\Account;
use App\SubArea;
use App\Distributor;
use App\StoreDistributor;
use App\Timezone;
use App\SalesTiers;
use App\Filters\StoreFilters;

class StoreController extends Controller
{

    public function getDataWithFilters(StoreFilters $filters){
        $data = Store::filter($filters)->get();
        return $data;
    }

    public function baca()
    {
        return view('store.store');
    }

    public function readStore()
    {
        $data['subarea']        = SubArea::get();
        $data['account']        = Account::get();
        $data['timezone']       = Timezone::get();
        $data['sales']    = SalesTiers::get();
        return view('store.storecreate', $data);
    }

    public function readUpdate($id)
    {
        $data['str']           = Store::where(['id' => $id])->first();
        $data['subarea']        = SubArea::get();
        $data['sales']          = SalesTiers::get();
        $data['timezone']       = Timezone::get();

        $data['account']        = Account::get();
        // return $data;
        return view('store.storeupdate', $data);
    }

    // public function getCity(Request $request) 
    // {
    //     $city = City::where('id_province', $request->get('id'))->get();
    //     return response()->json($city);
    // }

    public function store(Request $request)
    {
       $data=$request->all();
       $limit=[

        'name1'          => 'required',
        'address'        => 'required',
        'latitude'       => 'required',
        'longitude'      => 'required',
        'account'        => 'required|numeric',
        'subarea'        => 'required|numeric',
    ];
    $validator = Validator($data, $limit);
    if ($validator->fails()){
        return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    } else {
        $insert = Store::create([

            'name1'             => $request->input('name1'),
            'name2'             => $request->input('name2'),
            'address'           => $request->input('address'),
            'latitude'          => $request->input('latitude'),
            'coverage'          => $request->input('coverage'),
            'store_panel'       => $request->input('store_panel'),
            'is_vito'           => $request->input('is_vito'),
            'is_jawa'           => $request->input('is_jawa'),
            'delivery'          => $request->input('delivery'),
            'longitude'         => $request->input('longitude'),
            'id_account'        => $request->input('account'),
            'id_salestier'      => $request->input('sales'),
            'id_timezone'       => $request->input('timezone'),
            'id_account'        => $request->input('account'),
            'id_subarea'        => $request->input('subarea'),
        ]);
        return redirect()->route('store')
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah store!'
        ]);
    }
}

public function data()
{
    $store = Store::with([ 'account', 'subarea', 'sales'])
    ->select('stores.*');
    return Datatables::of($store)
    ->addColumn('action', function ($store) {
        return "<a href=".route('ubah.store', $store->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
        <button data-url=".route('store.delete', $store->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
    })
    ->addColumn('account', function($store) {
        return $store->account->name."(".$store->account->channel->name.")";
    })
    ->addColumn('sales', function($store) {
        return $store->sales->name."(".$store->sales->name.")";
    })
        // ->addColumn('distributor', function($store) {
        //     $dist = StoreDistributor::where(['id_store'=>$store->id])->get();
        //     $distList = array();
        //     foreach ($dist as $data) {
        //         array_push($distList,$data->distributor->name);
        //     }
        //     return rtrim(implode(',', $distList), ',');
        // })
    ->addColumn('subarea', function($store) {
        return $store->subarea->name."(".$store->subarea->area->name.")";
    })->make(true);
}

    public function exampleSheet()
    {
        Excel::create('Target Store'.Cabon::now(), function($excel){
            $excel->sheet('Store Format',function($sheet){
                $sheet->cells('A1:G1',function($cells){
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setBackground('yellow');
                });
                  $sheet->row(1, ['ID STORE', 'NAME 1', 'NAME 2', 'ADDRESS', 'LATITUDE', 'LONGITUDE', 'ACCOUNT','SUB AREA', 'TIMEZONE', 'SALES TIERS', 'IS VITO', 'IS JAWA', 'STORE PANEL', 'COVERAGE', 'DELIVERY']);
            });
            $excel->sheet('Timezone',function($sheet){
                $sheet->cells('A1:C1',function($cells){
                     $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setBackground('yellow');
                });
                $timezone = Timezone::orderBy('id','DESC')->get();
                foreach ($timezone as $row) {
                    $sheet->appendRow([
                        $row->id,
                        $row->name,
                        $row->timezone,
                    ]);
                }
            });

        })->export('xlsx');
        return;
    }

public function exportXLS()
{

 Excel::create('Report_Stores_'.Carbon::now(), function($excel){

    $excel->sheet('Store Format', function($sheet){
        $sheet->cells('A1:C1', function($cells) {
            $cells->setFontWeight('bold');
            $cells->setAlignment('center');
        });

        $sheet->row(1, ['ID STORE', 'NAME 1', 'NAME 2', 'ADDRESS', 'LATITUDE', 'LONGITUDE', 'ACCOUNT','SUB AREA', 'TIMEZONE', 'SALES TIERS', 'IS VITO', 'IS JAWA', 'STORE PANEL', 'COVERAGE', 'DELIVERY']);
        $oke=Store::orderBy('created_at','ASC')->get();
        foreach ($oke as $ok) {
            $sheet->appendRow([
                $ok->id, 
                $ok->name1, 
                $ok->name2,
                $ok->address,
                $ok->latitude,
                $ok->longitude,
                $ok->account->name,
                $ok->subarea->name,
                $ok->timezone->name,
                $ok->sales->name,
                $ok->is_vito,
                $ok->is_jawa,
                $ok->store_panel,
                $ok->coverage,
                $ok->delivery,
            ]);
        }
    });
    $excel->sheet('Timezone List', function($sheet) {
        $sheet->cells('A1:G1', function($cells) {
            $cells->setFontWeight('bold');
            $cells->setAlignment('center');
        });
        $sheet->row(1, ['ID TIMEZONE', 'NAME', 'TIMEZONE']);
        $oke=Timezone::orderBy('created_at','DESC')->get();
        foreach ($oke as $ok) {
            $sheet->appendRow([
                $ok->id, 
                $ok->name,
                $ok->timezone,
            ]);
        }
    });
})->export('xlsx');
 return ;
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
                        // return var_dump($results);
                        // exit();
                foreach($results as $row)
                {
                 echo "$row<hr>";
                            // CEK ISSET CUSTOMER
                 $dataArea['area_name']      = $row->area;
                 $dataArea['sub_area']    = $row->sub_area;
                 $id_area = $this->findArea($dataArea);


                 $insert = Store::create([
                    'name1' => $row->name1,
                    'name2' => $row->name2,
                    'address' => $row->address,
                    'latitude' => $row->latitude,
                    'longitude' => $row->longitude,
                    'id_account' => $row->id_account,
                    'id_subarea' => $row->sub_area,
                    'id_timezone' => $row->id_timezone,
                    'id_salestier' => $row->id_salestier,
                    'is_vito' => $row->is_vito,
                    'store_panel' => $row->store_panel,
                    'coverage' => $row->coverage,
                    'delivery' => $row->delivery,
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

public function findArea($data)
{
    $dataArea = Area::where('name','like','%'.trim($data['area_name']).'%');
    if ($dataArea->count() == 0) {

        $dataSubArea['sub_area']  = $data['sub_area'];
        $id_subarea = $this->finSubArea($dataSubArea);

        $area = Area::create([
          'name'        => $data['area_name'],
          'id_subarea'   => $id_subarea,
      ]);
        $id_area = $area->id;
    }else{
        $id_area = $dataArea->first()->id;
    }
    return $id_area;
}

public function finSubArea($data)
{
    $dataSubArea = SubArea::where('name','like','%'.trim($data['sub_area']).'%');
    if ($dataSubArea->count() == 0) {

        $subarea = SubArea::create([
          'name'        => $data['sub_area'],
      ]);
        $id_subarea = $subarea->id;
    }else{
        $id_subarea = $dataSubArea->first()->id;
    }
    return $id_subarea;
}


public function update(Request $request, $id) 
{
    $data=$request->all();
    $limit=[

        'name1'          => 'required',
        'address'        => 'required',
        'latitude'       => 'required',
        'longitude'      => 'required',
        'account'        => 'required|numeric',
        'subarea'        => 'required|numeric',
    ];
    $validator = Validator($data, $limit);
    if ($validator->fails()){
        return redirect()->back()
        ->withErrors($validator)
        ->withInput();
    } else {
        $store = Store::find($id);
        $store->name1             = $request->input('name1');
        $store->name2             = $request->input('name2');
        $store->address           = $request->input('address');
        $store->latitude          = $request->input('latitude');
        $store->longitude         = $request->input('longitude');
        $store->id_account        = $request->input('account');
        $store->id_subarea        = $request->input('subarea');
        $store->is_vito           = $request->input('is_vito');
        $store->is_jawa           = $request->input('is_jawa');
        $store->coverage           = $request->input('coverage');
        $store->delivery           = $request->input('delivery');
        $store->store_panel           = $request->input('store_panel');
        $store->save();
        return redirect()->route('store')
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah store!'
        ]);
    }
}

public function delete($id)
{
    $store = Store::find($id);
    $store->delete();
    return redirect()->back()
    ->with([
        'type'      => 'success',
        'title'     => 'Sukses!<br/>',
        'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
    ]);
}
}