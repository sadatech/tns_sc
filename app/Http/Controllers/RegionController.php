<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Region;
use App\Area;
use Auth;
use App\Filters\RegionFilters;
use Yajra\Datatables\Datatables;

class RegionController extends Controller
{
    public function baca()
    {
        return view('store.region');
    }

    public function data()
    {
        $region = Region::select('regions.*');;
        return Datatables::of($region)
        ->addColumn('action', function ($region) {
            return '<button onclick="editModal('.$region->id.',&#39;'.$region->name.'&#39;)" class="btn btn-sm btn-primary btn-square"><i class="si si-pencil"></i></button>
            <button data-url='.route("region.delete", $region->id).' class="btn btn-sm btn-danger btn-square js-swal-delete"><i class="si si-trash"></i></button>';
        })->make(true);
    }

    public function getDataWithFilters(RegionFilters $filters)
    {

        $data = Region::filter($filters)->get();
        return $data;
    }
    
    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = Region::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")->count();
            if ($check < 1) {
                Region::create([
                    'name'       => $request->input('name'),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Region!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Region sudah ada!'
                ]);
            }
        }
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = Region::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")->count();
            if ($check < 1) {
                $region = Region::find($id);
                    $region->name = $request->get('name');
                    $region->save();
                    return redirect()->back()
                    ->with([
                        'type'    => 'success',
                        'title'   => 'Sukses!<br/>',
                        'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah     region!'
                    ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Region sudah ada!'
                ]);
            }
        }
    }

    // ini buat warning data apa aja yang bakalan ke hapus
    public function deleteConfirm($id)
    {
        $data['data'] = Area::where([
            'id_region' => $id
        ])->get();
        $data['title'] = "Region";
        return view('delete_confirmation', $data);
    }

    public function delete($id)
    {
        $region = Region::find($id);
            $area = Area::where(['id_region' => $region->id])->count();
            if (!$area < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain (area, sub-area, dan store)!'
                ]);
            } else {
                $region->delete();
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
                ]);
            }
    }
}
