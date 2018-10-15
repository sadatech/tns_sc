<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use App\Store;
use App\Account;
use App\SubArea;
use App\Distributor;
use App\StoreDistributor;
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
        $data['distributor']    = Distributor::get();
        $data['account']        = Account::get();
        return view('store.storecreate', $data);
    }

    public function readUpdate($id)
    {
        $data['str'] 		    = Store::where(['id' => $id])->first();
        $data['subarea']        = SubArea::get();
        $data['distributor']    = Distributor::get();
        $data['account']        = Account::get();
        $dist                   = StoreDistributor::where(['id_store'=>$id])->get(['id_distributor']);
        $distList = array();
        foreach ($dist as $value) {
            $distList[] = $value->id_distributor;
        }
        $data['dist'] = json_encode($distList);
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
            'photo'          => 'max:10000|mimes:jpeg,jpg,bmp,png',
            'name1'          => 'required',
            'owner_phone'    => 'required',
            'address'        => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'type'           => 'required',
            'account'        => 'required|numeric',
            'distributor'    => 'required',
            'subarea'        => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            if($request->file('photo')) {
                $logo = $data['photo'];
                $foto = Str::random().time()."_".rand(1,99999).".".$logo->getClientOriginalExtension();
                $Path = 'uploads/logoStore';
                $logo->move($Path, $foto);
            } else {
                $foto = "default.png";
            }
            $insert = Store::create([
                'photo'             => $foto,
                'name1'             => $request->input('name1'),
                'name2'             => $request->input('name2'),
                'address'           => $request->input('address'),
                'latitude'          => $request->input('latitude'),
                'longitude'         => $request->input('longitude'),
                'type'              => $request->input('type'),
                'id_account'        => $request->input('account'),
                'id_subarea'        => $request->input('subarea'),
            ]);
            if ($insert) 
            {
                $dataStore = array();
                foreach ($request->input('distributor') as $distributor) {
                    $dataStore[] = array(
                        'id_distributor'    => $distributor,
                        'id_store'          => $insert->id,
                    );
                }
                DB::table('store_distributors')->insert($dataStore); 
                return redirect()->route('store')
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah store!'
                ]);
            }
        }
    }  

    public function data()
    {
        $store = Store::with(['province', 'city', 'distributor', 'account', 'subarea'])
        ->select('stores.*');
        return Datatables::of($store)
        ->addColumn('action', function ($store) {
            return "<a href=".route('ubah.store', $store->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('store.delete', $store->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
            <a href=".asset('/uploads/documents')."/".$store->photo." class='btn btn-sm btn-success btn-square popup-image' title='Show Photo Store'><i class='si si-picture mr-2'></i>Photo</a>";
        })
        ->addColumn('account', function($store) {
            return $store->account->name."(".$store->account->channel->name.")";
        })
        ->addColumn('distributor', function($store) {
            $dist = StoreDistributor::where(['id_store'=>$store->id])->get();
            $distList = array();
            foreach ($dist as $data) {
                $distList[] = $data->distributor->name;
            }
            return rtrim(implode(',', $distList), ',');
        })
        ->addColumn('subarea', function($store) {
            return $store->subarea->name."(".$store->subarea->area->name.")";
        })->make(true);
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'photo'          => 'max:10000|mimes:jpeg,jpg,bmp,png',
            'name1'          => 'required',
            'address'        => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'type'           => 'required',
            'account'        => 'required|numeric',
            'distributor'    => 'required',
            'subarea'        => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $store = Store::find($id);
            if ($request->file('photo')) {
                $photo = $request->file('photo');
                $foto_str = Str::random().time()."_".rand(1,99999).".".$photo->getClientOriginalExtension();
                $str_path = 'uploads/logoStore';
                $photo->move($str_path, $foto_str);
            } else {
                $foto_str = "Change Photo Store Failed";
            }
                if($request->file('photo')){
                    $store->photo = $foto_str;
                }
                if ($request->input('distributor')) {
                    foreach ($request->input('distributor') as $distributor) {
                        StoreDistributor::where('id_store', $id)->delete();
                        $dataStore[] = array(
                            'id_distributor'    => $distributor,
                            'id_store'          => $id,
                        );
                    }
                    DB::table('store_distributors')->insert($dataStore);
                }
                $store->name1             = $request->input('name1');
                $store->name2             = $request->input('name2');
                $store->address           = $request->input('address');
                $store->latitude          = $request->input('latitude');
                $store->longitude         = $request->input('longitude');
                $store->type              = $request->input('type');
                $store->id_account        = $request->input('account');
                // $store->id_distributor    = $request->input('distributor');
                $store->id_subarea        = $request->input('subarea');
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