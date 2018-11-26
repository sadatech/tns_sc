<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use DB;
use Auth;
use File;
use Excel;
use App\Area;
use App\Region;
use App\Store;
use App\Pasar;
use Carbon\Carbon;
use App\SubArea;
use Rap2hpoutre\FastExcel\FastExcel;
use Yajra\Datatables\Datatables;
use App\Filters\SubAreaFilters;

class SubareaController extends Controller
{
    public function baca()
    {
        $data['area'] = Area::get();
        return view('store.subarea', $data);
    }

    public function data()
    {
        $subarea = SubArea::with('area.region')
        ->select('sub_areas.*');
        return Datatables::of($subarea)
        ->addColumn('action', function ($subarea) {
            $data = array(
                'id'        => $subarea->id,
                'area'      => $subarea->area->id,
                'area_name' => $subarea->area->name,
                'name'      => $subarea->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('subarea.delete', $subarea->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function getDataWithFilters(SubAreaFilters $filters){
        $data = SubArea::filter($filters)->get();
        return $data;
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'      => 'required',
            'area'      => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else { 
            $data = Area::where(['id' => $request->input('area')])->first();
            $check = SubArea::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")
            ->where(['id_area' => $data->id])->count();
            if ($check < 1) {
                SubArea::create([
                    'name'       => $request->input('name'),
                    'id_area'  => $request->input('area'),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Sub Area!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>SubArea atau Area sudah ada!'
                ]);
            }
        }
    }
    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'name'      => 'required',
            'area'      => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = Area::where(['id' => $request->get('area')])->first();
            $check = SubArea::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->get('name'))."'")
            ->where(['id_area' => $data->id])->count();
            if ($check < 1) 
            { 
                $subarea = SubArea::find($id);
                    $subarea->name      = $request->get('name');
                    $subarea->id_area   = $request->get('area');
                    $subarea->save();
                    return redirect()->back()
                    ->with([
                        'type'    => 'success',
                        'title'   => 'Sukses!<br/>',
                        'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Sub Area!'
                    ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>SubArea atau Area sudah ada!'
                ]);
            }
        }
    }   

    public function delete($id) {
        $subarea = SubArea::find($id);
            $store = Store::where([
                'id_subarea' => $subarea->id
            ])->count();
            $pasar = Pasar::where([
                'id_subarea' => $subarea->id
            ])->count();
            $jumlah = $store + $pasar;
            if (!$jumlah < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain (area, region, pasar, dan store)!'
                ]);
            } else {
                $subarea->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
                ]);
            }
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
                            echo "$row<hr>";
                            // CEK ISSET CUSTOMER
                            $dataArea['area_name']      = $row->area;
                            $dataArea['region_name']    = $row->region;
                            $id_area = $this->findArea($dataArea);

                            SubArea::create([
                                'id_area'       => $id_area,
                                'name'          => $row->sub_area,
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

    public function exportXLS()
    {
 
        $subarea = SubArea::orderBy('created_at', 'DESC')->get();
        $filename = "subareas_".Carbon::now().".xlsx";
        (new FastExcel($subarea))->download($filename, function ($subarea) {
            return [
                'Sub Area'  => $subarea->name,
                'Area'      => $subarea->area->name,
                'Region'    => $subarea->area->region->name
            ];
        });
    }
    
    public function findArea($data)
    {
        $dataArea = Area::where('name','like','%'.trim($data['area_name']).'%');
        if ($dataArea->count() == 0) {
            
            $dataRegion['region_name']  = $data['region_name'];
            $id_region = $this->findRegion($dataRegion);

            $area = Area::create([
              'name'        => $data['area_name'],
              'id_region'   => $id_region,
            ]);
            $id_area = $area->id;
        }else{
            $id_area = $dataArea->first()->id;
        }
      return $id_area;
    }

    public function findRegion($data)
    {
        $dataRegion = Region::where('name','like','%'.trim($data['region_name']).'%');
        if ($dataRegion->count() == 0) {
            
            $region = Region::create([
              'name'        => $data['region_name'],
            ]);
            $id_region = $region->id;
        }else{
            $id_region = $dataRegion->first()->id;
        }
      return $id_region;
    }
}
