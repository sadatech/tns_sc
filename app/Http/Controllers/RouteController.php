<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\SubArea;
use App\Route;

class RouteController extends Controller
{
    public function baca()
    {
        $data['subarea'] = SubArea::get();
        return view('store.root', $data);
    }

    public function data()
    {
        $subarea = Route::with('subarea.area.region')
        ->select('routes.*');
        return Datatables::of($subarea)
        ->addColumn('action', function ($subarea) {
            $data = array(
                'id'            => $subarea->id,
                'subarea'       => $subarea->subarea->id,
                'subarea_name'  => $subarea->subarea->name,
                'name'          => $subarea->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('root.delete', $subarea->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            'subarea'      => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else { 
            $data = SubArea::where(['id' => $request->input('subarea')])->first();
            $check = Route::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")
            ->where(['id_subarea' => $data->id])->count();
            if ($check < 1) {
                Route::create([
                    'name'          => $request->input('name'),
                    'id_subarea'    => $request->input('subarea'),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Route!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Route sudah ada!'
                ]);
            }
        }
    }
    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            'subarea'      => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = SubArea::where(['id' => $request->get('subarea')])->first();
            $check = Route::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->get('name'))."'")
            ->where(['id_subarea' => $data->id])->count();
            if ($check < 1) 
            { 
                $subarea = Route::find($id);
                    $subarea->name      = $request->get('name');
                    $subarea->id_subarea   = $request->get('subarea');
                    $subarea->save();
                    return redirect()->back()
                    ->with([
                        'type'    => 'success',
                        'title'   => 'Sukses!<br/>',
                        'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Route!'
                    ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Route sudah ada!'
                ]);
            }
        }
    }   

    public function delete($id) 
    {
        $subarea = Route::find($id); 
        $subarea->delete();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
    }

    // public function importXLS(Request $request)
    // {

    //     $this->validate($request, [
    //         'file' => 'required'
    //     ]);

    //     $transaction = DB::transaction(function () use ($request) {
    //         $file = Input::file('file')->getClientOriginalName();
    //         $filename = pathinfo($file, PATHINFO_FILENAME);
    //         $extension = pathinfo($file, PATHINFO_EXTENSION);

    //         if ($extension != 'xlsx' && $extension !=  'xls') {
    //             return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
    //         }
    //         if($request->hasFile('file')){
    //             $file = $request->file('file')->getRealPath();
    //             $ext = '';
                
    //             Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results)
    //                 {
    //                     foreach($results as $row)
    //                     {
    //                         echo "$row<hr>";
    //                         // CEK ISSET CUSTOMER
    //                         $dataArea['area_name']      = $row->area;
    //                         $dataArea['region_name']    = $row->region;
    //                         $id_area = $this->findArea($dataArea);

    //                         SubArea::create([
    //                             'id_area'       => $id_area,
    //                             'name'          => $row->sub_area,
    //                         ]);
    //                     }
    //                 },false);
    //         }
    //         return 'success';
    //     });

    //     if ($transaction == 'success') {
    //         return redirect()->back()
    //             ->with([
    //                 'type'      => 'success',
    //                 'title'     => 'Sukses!<br/>',
    //                 'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil import!'
    //             ]);
    //     }else{
    //         return redirect()->back()
    //             ->with([
    //                 'type'    => 'danger',
    //                 'title'   => 'Gagal!<br/>',
    //                 'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
    //             ]);
    //     }
    // }

    // public function exportXLS()
    // {
 
    //     $subarea = SubArea::orderBy('created_at', 'DESC')->get();
    //     $filename = "subareas_".Carbon::now().".xlsx";
    //     (new FastExcel($subarea))->download($filename, function ($subarea) {
    //         return [
    //             'Sub Area'  => $subarea->name,
    //             'Area'      => $subarea->area->name,
    //             'Region'    => $subarea->area->region->name
    //         ];
    //     });
    // }
    
    // public function findArea($data)
    // {
    //     $dataArea = Area::where('name','like','%'.trim($data['area_name']).'%');
    //     if ($dataArea->count() == 0) {
            
    //         $dataRegion['region_name']  = $data['region_name'];
    //         $id_region = $this->findRegion($dataRegion);

    //         $area = Area::create([
    //           'name'        => $data['area_name'],
    //           'id_region'   => $id_region,
    //         ]);
    //         $id_area = $area->id;
    //     }else{
    //         $id_area = $dataArea->first()->id;
    //     }
    //   return $id_area;
    // }

    // public function findRegion($data)
    // {
    //     $dataRegion = Region::where('name','like','%'.trim($data['region_name']).'%');
    //     if ($dataRegion->count() == 0) {
            
    //         $region = Region::create([
    //           'name'        => $data['region_name'],
    //         ]);
    //         $id_region = $region->id;
    //     }else{
    //         $id_region = $dataRegion->first()->id;
    //     }
    //   return $id_region;
    // }
}

