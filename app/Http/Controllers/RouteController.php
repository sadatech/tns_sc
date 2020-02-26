<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\SubArea;
use App\Area;
use App\Region;
use App\Route;
use App\Traits\StringTrait;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Filters\RouteFilters;

class RouteController extends Controller
{
    use StringTrait;

    public function getDataWithFilters(RouteFilters $filters)
    {
        $data = Route::filter($filters)->get();
        return $data;
    }

    public function baca($market = '')
    {
        $data['subarea'] = SubArea::get();
        $data['market']  = $market;
        return view('store.route', $data);
    }

    public function data(RouteFilters $filters, $market = '')
    {
        $route = Route::filter($filters)
            ->with('subarea.area.region')
            ->when($market == '1', function($q){
                return $q->whereType(1);
            })
            ->when($market == '2', function($q){
                return $q->whereType(2);
            })
            ->orderBy('routes.id', 'desc')
            ->select('routes.*');

            return Datatables::of($route)
            ->addColumn('action', function ($routes) use ($market){
                $data = array(
                    'id'            => $routes->id,
                    'type'          => $routes->type,
                    'sub_area_id'   => $routes->subarea->id,
                    'sub_area_name' => $routes->subarea->name,
                    'route'         => $routes->name,
                    'latitude'      => $routes->latitude,
                    'longitude'     => $routes->longitude,
                    'address'       => $routes->address,
                );
                return "<button onclick='editModalRoute(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' data-target='#formModal' data-toggle='modal'><i class='si si-pencil'></i></button>
                <button data-url=".route('route.delete',['market'=>$market, 'id'=>$routes->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
            })->make(true);
    }

    public function store(Request $request)
    {
        $request['name'] = $request->route;

        return $data  = $request->all();
        $limit = [
            'name'          => 'required',
            'type'          => 'required|numeric',
            'id'            => 'nullable|numeric',
            'sub_area'      => 'nullable|string',
            'area'          => 'nullable|string',
            'region'        => 'nullable|string',
            'new_sub_area'  => 'nullable|string',
            'new_area'      => 'nullable|string',
            'new_region'    => 'nullable|string',
            'latitude'      => 'nullable|string',
            'longitude'     => 'nullable|string',
            'address'       => 'nullable|string',
            'type'          => 'nullable|numeric',
            'update'        => 'nullable|numeric',
        ];

        $success   = 'true';

        $validator = Validator($data, $limit);
        if ($validator->fails()){
            $result = [
                'status' => false,
                'type'   => 'warning',
                'title'  => 'Warning!<br/>',
                'error'  => $validator,
                'message'=> '<i class="em em-confounded mr-2"></i>Gagal '.$actionType.' Route (validator)!'
            ];
            $success   = 'false';
        }
        
        $actionType = ($request->update == 1 ? 'mengubah' : 'menambah');



        if ($success == 'true') {

            if (!$request->has('s_sub_area')) {
                if (!empty($request->new_sub_area)) {

                    if (!$request->has('s_area')) {
                    
                        if (!$request->has('s_region')) {
                            if (!empty($request->new_region)) {
                                $region = Region::firstOrCreate([ 'name' => $this->trimAndUpper($request->new_region) ]);
                            }else{
                                $success = 'Choose or input the Region.';
                            }
                        }else{
                            $region = Region::where([ 'id' => $this->getFirstExplode($request->s_region, '`^') ])->first();
                        }

                        if (!empty($request->new_area)) {
                            $area = Area::firstOrCreate([ 'name' => $this->trimAndUpper($request->new_area), 'id_region' => $region->id ]);
                        }else{
                            $success = 'Choose or input the Area.';
                        }
                    }else{
                        $area = Area::where([ 'id' => $this->getFirstExplode($request->s_area, '`^') ])->first();
                    }

                    if (!empty($request->new_sub_area) && $success == 'true') {
                        $subArea = SubArea::firstOrCreate([ 'name' => $this->trimAndUpper($request->new_sub_area), 'id_area' => $area->id ]);
                    }else{
                        $success = 'Choose or input the Sub Area correctly.';
                    }

                }else{
                    $success = 'Choose the Sub Area.';
                }
            }else{
                $subArea = SubArea::where([ 'id' => $this->getFirstExplode($request->s_sub_area, '`^') ])->first();
            }

            if ($success == 'true') {
                $check  = Route::whereRaw("TRIM(UPPER(name)) = '". $this->trimAndUpper($request->name)."'")
                    ->where(['id_subarea' => $subArea->id])->count();

                if ($request->update == 1) {
                    Route::whereId($request->id)
                        ->update([
                            'name'          => $request->name,
                            'type'          => $request->type,
                            'latitude'      => $request->latitude,
                            'longitude'     => $request->longitude,
                            'address'       => $request->address,
                            'id_subarea'    => $subArea->id,
                        ]);
                }else{
                    Route::create([
                            'name'          => $request->name,
                            'type'          => $request->type,
                            'latitude'      => $request->latitude,
                            'longitude'     => $request->longitude,
                            'address'       => $request->address,
                            'id_subarea'    => $subArea->id,
                        ]);
                }

                $result = [
                    'status'  => true,
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil '.$actionType.' Route!'
                ];
            } else {
                $keys = [];
                foreach ($request->all() as $key => $value) {
                    $keys[] = $key;
                }
                $keys = implode(', ', $keys);
                $result = [
                    'status' => false,
                    'type'   => 'warning',
                    'title'  => 'Warning!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Gagal '.$actionType.' Route (select tree)!\n'.$success.'\n'.$keys
                ];
            }
        } else {
            $result = [
                'status' => false,
                'type'   => 'warning',
                'title'  => 'Warning!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Gagal '.$actionType.' Route!'
            ];
        }

        return response()->json($result, 200);
    }

    public function delete($id) 
    {
        $route = Route::find($id);
        $route->delete();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
    }

    public function exportXLS($market = '')
    {
        $route = Route::with('subarea.area.region')
            ->when($market == '1', function($q){
                return $q->whereType(1);
            })
            ->when($market == '2', function($q){
                return $q->whereType(2);
            })
            ->orderBy('id', 'DESC')
            ->get();
        $filename = "route_".Carbon::now().".xlsx";
        (new FastExcel($route))->download($filename, function ($route) {
            return [
                'Name'      => $route->name,
                'Sub Area'  => $route->subarea->name,
                'Area'      => $route->subarea->area->name,
                'Region'    => $route->subarea->area->region->name,
                'Address'   => $route->address,
                'Latitude'  => $route->latitude,
                'Longitude' => $route->longitude,
            ];
        });
    }

}

