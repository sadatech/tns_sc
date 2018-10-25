<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pasar;
use App\Timezone;
use App\SubArea;
use Yajra\Datatables\Datatables;

class PasarController extends Controller
{
    public function read()
    {
        return view('store.pasar');
    }

    public function readStore()
    {
        $data['subarea']        = SubArea::get();
        $data['timezone']       = Timezone::get();
        return view('store.pasarCreate', $data);
    }

    public function readUpdate($id)
    {
        $data['str']            = Pasar::where(['id' => $id])->first();
        $data['subarea']        = SubArea::get();
        $data['timezone']       = Timezone::get();
        return view('store.pasarUpdate', $data);
    }

    public function data()
    {
        $store = Pasar::with(['subarea', 'timezone'])
        ->select('pasars.*');
        return Datatables::of($store)
        ->addColumn('action', function ($store) {
            return "<a href=".route('ubah.pasar', $store->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('pasar.delete', $store->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'           => 'required',
            'address'        => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'timezone'       => 'required',
            'subarea'        => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = SubArea::where(['id' => $request->input('subarea')])->first();
            $check = Pasar::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($request->input('name')))."'")
            ->where(['id_subarea' => $data->id])->count();
            if ($check < 1) {
                Pasar::create([
                    'name'              => $request->input('name'),
                    'phone'             => $request->input('phone'),
                    'address'           => $request->input('address'),
                    'latitude'          => $request->input('latitude'),
                    'longitude'         => $request->input('longitude'),
                    'id_timezone'       => $request->input('timezone'),
                    'id_subarea'        => $request->input('subarea')
                ]);
                return redirect()->route('pasar')
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Pasar!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Nama pasar dengan subarea yang sama sudah ada!'
                ]);
            }
        }
    }  

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'name'           => 'required',
            'address'        => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'timezone'       => 'required',
            'subarea'        => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = SubArea::where(['id' => $request->input('subarea')])->first();
            $check = Pasar::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($request->input('name')))."'")
            ->where(['id_subarea' => $data->id])->count();
            if ($check < 1) {
                $dataPasar = Pasar::find($id);
                $dataPasar->name              = $request->input('name');
                $dataPasar->phone             = $request->input('phone');
                $dataPasar->address           = $request->input('address');
                $dataPasar->latitude          = $request->input('latitude');
                $dataPasar->longitude         = $request->input('longitude');
                $dataPasar->id_subarea        = $request->input('subarea');
                $dataPasar->id_timezone       = $request->input('timezone');
                $dataPasar->save();
                return redirect()->route('pasar')
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Pasar!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Nama pasar dengan subarea yang sama sudah ada!'
                ]);
            }
        }
    }

    public function delete($id)
    {
        $dataPasar = Pasar::find($id);
            $dataPasar->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
}
