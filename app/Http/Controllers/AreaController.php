<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\ProductPromo;
use App\ProductFokusGtc;
use App\Area;
use App\SubArea;
use App\Region;
use App\Filters\AreaFilters;
use Yajra\Datatables\Datatables;

class AreaController extends Controller
{
    public function index()
    {
        $data['region'] = Region::get();
        return view('store.area', $data);
    }
    
    public function data()
    {
        $area = Area::with('region')
        ->select('areas.*');
        return Datatables::of($area)
        ->addColumn('action', function ($area) {
            $data = array(
                'id'            => $area->id,
                'region'        => $area->region->id,
                'region_name'   => $area->region->name,
                'name'          => $area->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('area.delete', $area->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function getDataWithFilters(AreaFilters $filters)
    {
        
        $data = Area::filter($filters)->get();
        return $data;
    }

    public function store(Request $request)
    { 
        $data=$request->all();
        $limit=[
            'name'       => 'required',
            'region'     => 'required|numeric'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = Region::where(['id' => $request->input('region')])->first();
            $check = Area::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")
            ->where(['id_region' => $data->id])->count();
            // dd($check);
            if ($check < 1) {
                Area::create([
                    'name'       => $request->input('name'),
                    'id_region'  => $request->input('region'),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Area!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Area atau Region sudah ada!'
                ]);
            }
        }
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'name'      => 'required',
            'region'    => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = Region::where(['id' => $request->get('region')])->first();
            $check = Area::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->get('name'))."'")
            ->where(['id_region' => $data->id])->count();
            if ($check < 1) 
            {
                $area = Area::find($id);
                    $area->name         = $request->get('name');
                    $area->id_region    = $request->get('region');
                    $area->save();
                    return redirect()->back()
                    ->with([
                        'type'    => 'success',
                        'title'   => 'Sukses!<br/>',
                        'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah area!'
                    ]);
            } else  {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Area dan Region sudah ada!'
                ]);
            }
        }  
    }

    public function delete($id) 
    {
        $area = Area::find($id);

        $SubArea = SubArea::where(['id_area' => $area->id])->count();
        $ProductFokusGtc = ProductFokusGtc::where(['id_area' => $area->id])->count();

        if (!$SubArea < 1 || !$ProductFokusGtc < 1)
        {
            return redirect()->back()
            ->with([
                'type'    => 'danger',
                'title'   => 'Gagal!<br/>',
                'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain (region, area, store, promo, dan target product)!'
            ]);
        } else {
            $area->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
           ]);
        }

    }
}
