<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use File;
use Excel;
use App\Place;

class PlaceController extends Controller
{
    public function baca()
    {
        return view('store.place');
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
            'name'          => 'required',
            'code'          => 'required|string',
            'address'       => 'required',
            'description'   => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            Place::create([
                'name'          => $request->input('name'),
                'email'         => $request->input('email'),
                'phone'         => $request->input('phone'),
                'code'          => $request->input('code'),
                'address'       => $request->input('address'),
                'latitude'      => $request->input('latitude'),
                'longitude'     => $request->input('longitude'),
                'description'   => $request->input('description'),
            ]);
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah place!'
            ]);
        }
    }  

    public function data()
    {
        $place = Place::select('places.*');
        return Datatables::of($place)
        ->addColumn('action', function ($place) {
            $data = array(
                'id'            => $place->id,
                'name'          => $place->name,
                'code'          => $place->code,        
                'address'       => $place->address,
                'phone'         => $place->phone,    
                'email'         => $place->email,
                'latitude'      => $place->latitude,
                'longitude'     => $place->longitude,
                'description'   => $place->description
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('place.delete', $place->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'name'        => 'required',
            'code'        => 'required',
            'address'     => 'required',
            'latitude'    => 'required',
            'longitude'   => 'required',
            'description' => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $place = Place::find($id);
                $place->name        = $request->input('name');
                $place->code        = $request->input('code');
                $place->email       = $request->input('email');
                $place->phone       = $request->input('phone');
                $place->address     = $request->input('address');
                $place->latitude    = $request->input('latitude');
                $place->longitude   = $request->input('longitude');
                $place->description = $request->input('description');
                $place->save();
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah place!'
                ]);
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' =>   'required|mimeTypes:'.
                        'application/vnd.ms-office,'.
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'.
                        'application/vnd.ms-excel'
        ]);
        if($request->hasFile('file')){
            Excel::load($request->file('file')->getRealPath(), function ($reader) {
                foreach ($reader->toArray() as $key => $row) {
                    $data['code']           = $row['code'];
                    $data['name']           = $row['name'];
                    $data['email']          = $row['email'];
                    $data['phone']          = $row['phone'];
                    $data['longitude']      = $row['longitude'];
                    $data['latitude']       = $row['latitude'];
                    $data['address']        = $row['address'];
                    $data['description']    = $row['description'];

                    if(!empty($data)) {
                        DB::table('places')->insert($data);
                    } else {
                        return redirect()->back()
                            ->with([
                                'type'    => 'danger',
                                'title'   => 'Gagal!<br/>',
                                'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
                            ]);
                    }
                }
            });
        }
        return redirect()->back()
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil Mengimport Data place!'
        ]);
    }

    public function delete($id)
    {
        $place = Place::find($id);
            $place->delete();
            return redirect()->back()
            ->with([
                'type'    => 'success',
                'title'   => 'Sukses!<br/>',
                'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
}