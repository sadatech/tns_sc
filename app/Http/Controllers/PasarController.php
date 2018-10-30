<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Pasar;
use App\SubArea;
use App\Area;
use App\Region;
use DB;
use Auth;
use File;
use Excel;

class PasarController extends Controller
{
    public function read()
    {
        return view('store.pasar');
    }

    public function readStore()
    {
        $data['subarea']        = SubArea::get();
        return view('store.pasarcreate', $data);
    }

    public function readUpdate($id)
    {
        $data['str']            = Pasar::where(['id' => $id])->first();
        $data['subarea']        = SubArea::get();
        return view('store.pasarupdate', $data);
    }

    public function data()
    {
        $store = Pasar::with('subarea.area.region')
        ->select('pasars.*');
        return Datatables::of($store)
        ->addColumn('action', function ($store) {
            return "<div align='center'><a href=".route('ubah.pasar', $store->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('pasar.delete', $store->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button></div>";
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
                    'address'           => $request->input('address'),
                    'latitude'          => $request->input('latitude'),
                    'longitude'         => $request->input('longitude'),
                    'id_subarea'        => $request->input('subarea')
                ]);
                return redirect()->route('pasar')
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Pasar!'
                ]);
            } else {
                return redirect()->route('pasar')
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
                $dataPasar->address           = $request->input('address');
                $dataPasar->latitude          = $request->input('latitude');
                $dataPasar->longitude         = $request->input('longitude');
                $dataPasar->id_subarea        = $request->input('subarea');
                $dataPasar->save();
                return redirect()->route('pasar')
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Pasar!'
                ]);
            } else {
                return redirect()->route('pasar')
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

    public function importXLS(Request $request)
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
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results)
                {
                    foreach($results as $row)
                    {
                        echo "$row<hr>";
                        $dataSub['subarea_name']   = $row->subarea;
                        $dataSub['area_name']      = $row->area;
                        $dataSub['region_name']    = $row->region;
                        $id_subarea = $this->findSub($dataSub);

                         Pasar::create([
                            'name'              => $row->pasar,
                            'id_subarea'        => $id_subarea,
                            'longitude'         => (isset($row->longitude)? $row->longitude : "-"),
                            'latitude'          => (isset($row->latitude) ? $row->latitude : "-"),
                            'address'           => $row->address
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

    public function findSub($data)
    {
        $dataSub = SubArea::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['subarea_name']))."'");
        if ($dataSub->count() < 1) {
            $dataSub['area_name']  = $data['area_name'];
            $dataSub['region_name']  = $data['region_name'];
            $id_area = $this->findArea($dataSub);
            if ($id_area) {
                $subarea = SubArea::create([
                    'name'        => $data['subarea_name'],
                    'id_area'     => $id_area
                ]);
                $id_subarea = $subarea->id;
                if ($id_subarea) {
                    return $id_subarea;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return $dataSub->first()->id;;
        }
    }

    public function findArea($data)
    {  
        $dataRegion['region_name']  = $data['region_name'];
        $id_region = $this->findRegion($dataRegion);
        $data1 = Region::where(['id' => $id_region])->first();
        $dataArea = Area::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['area_name']))."'")
        ->where(['id_region' => $data1->id]);
        if ($dataArea->count() < 1) {
            $area = Area::create([
              'name'        => $data['area_name'],
              'id_region'   => $id_region,
          ]);
            $id_area = $area->id;
        } else {
            return false;
        }
        return $id_area;
    }

    public function findRegion($data)
    {
        $dataRegion = Region::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['region_name']))."'");
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

    public function exportXLS()
    {
        $pasar = Pasar::orderBy('created_at', 'DESC')->get();
        $data = array();
        foreach ($pasar as $val) {
            $data[] = array(
                'Pasar'             => $val->name,
                'Address'           => $val->address,
                'Longitude'         => $val->longitude,
                'Latitude'          => $val->latitude,
                'Subarea'           => $val->subarea->name,
                'Area'              => $val->subarea->area->name,
                'Region'            => $val->subarea->area->region->name
            );
        }
        $filename = "Market_".Carbon::now().".xlsx";
        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('Market', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download();
    }

}
