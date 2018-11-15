<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailIn;
use App\SellIn;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use App\Category;
use App\Area;
use App\Account;
use App\DisplayShare;
use App\DetailDisplayShare;
use App\AdditionalDisplay;
use App\DetailAdditionalDisplay;
use App\Employee;
use App\EmployeeStore;
use App\Store;
use App\EmployeeSubArea;
use Auth;
use DB;
use Carbon\Carbon;
use App\StoreDistributor;
use App\Distributor;

class ReportController extends Controller
{
    // *********** SELL IN ****************** //

	public function sellInIndex(){
		return view('report.sellin');
	}

	public function sellInData(){

		$str =  "
                SELECT d.id, h.week, reg.name as region, ar.name as area, sar.name as sub_area, acc.name as account, cha.name as channel, str.name1 as store_name_1, str.name2 as store_name_2, emp.nik, emp.name as employee_name, h.date, pro.name as product_name, cat.name as category, d.qty as quantity, d.price as unit_price, (d.qty * d.price) as value, (d.qty * d.price * d.is_pf) as value_pf, spv.name as spv_name, str.id as storeId
                FROM detail_ins d
                JOIN sell_ins h ON h.id = d.id_sellin
                JOIN stores str ON str.id = h.id_store
                JOIN sub_areas sar ON sar.id = str.id_subarea
                JOIN areas ar ON ar.id = sar.id_area
                JOIN regions reg ON reg.id = ar.id_region
                JOIN accounts acc ON acc.id = str.id_account
                JOIN channels cha ON cha.id = acc.id_channel
                JOIN employees emp ON emp.id = h.id_employee
                JOIN products pro ON pro.id = d.id_product
                JOIN categories cat ON cat.id = pro.id_category
                JOIN employee_spvs ems ON ems.id_employee = emp.id
                JOIN employees spv ON ems.id_user = spv.id
                ";

        return Datatables::of(collect(DB::select($str)))
        ->addColumn('distributor_code', function($item) {
            $distributor_ids = StoreDistributor::where('id_store', $item->storeId)->pluck('id_distributor')->toArray();
            $distributor_code = implode(', ', Distributor::whereIn('id', $distributor_ids)->pluck('code')->toArray());

            return $distributor_code;
        })
        ->addColumn('distributor_name', function($item) {
            $distributor_ids = StoreDistributor::where('id_store', $item->storeId)->pluck('id_distributor')->toArray();
            $distributor_name = implode(', ', Distributor::whereIn('id', $distributor_ids)->pluck('name')->toArray());

            return $distributor_name;
        })
        ->addColumn('action', function ($item) {
            $data = array(
                'id'            => $item->id,
                'qty'          	=> $item->quantity
            );

            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('sellin.delete', $item->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
            ";
        })->make(true);
    
	}

	public function sellInUpdate(Request $request, $id) 
    {

        $data = DetailIn::find($id);
            $data->qty          = $request->get('qty');
            $data->save();
            return redirect()->back()
            ->with([
              'type'    => 'success',
              'title'   => 'Sukses!<br/>',
              'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah quantity penjualan!'
          ]);
    }

	public function sellInDelete($id)
    {
    	$data = DetailIn::find($id);
            $data->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
        
    }

    public function sellInAdd(Request $request){

    	return $request->all();

    	$data=$request->all();
        $limit=[
            'employee'          => 'required',
            'store'     => 'required',
            'product'     => 'required',
            'date'     => 'required',
            'qty'      => 'required|numeric'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
        	try{
        		DB::transaction(function () use ($request) {

        			// CEK APAKAH UDAH ADA DATA DI PENJUALAN HARI ITU ATAU GA
        			$header = SellIn::where('id_employee', $request->input('employee'))
        						->where('id_store', $request->input('store'))
        						->whereDate('date', $request->input('date'))
        						->first();

        			// INSERT HEADER & DETAIL
        			if(!$header){

        				// HEADER
                        // $header = SellIn::create([
                        //                 'id_store' => $request->input('store'),
                        //                 'id_employee' => $request->input('employee'),
                        //                 'week' => Carbon::now()->weekOfMonth,
                        //                 'date' => Carbon::now()
                        //             ]);

                        // ITUNG PRICE & IS PF

                        // $detail = DetailIn::create([
                        //                 'id_sellin' => $header->id,
                        //                 'id_product' => $request->input('product'),
                        //                 'qty' => $request->input('qty'),
                        //                 'date' => Carbon::now()
                        //             ]);

        			}


        		});
        	}catch(\Exception $e){
        		return redirect()->back()
	            ->with(['type'  => 'danger',
	                'title'     => 'Terjadi Kesalahan!<br/>',
	                'message'   => '<i class="em em-face_with_symbols_on_mouth mr-2"></i>Gagal untuk menambahkan data!'
	            ]);
        	}            
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Sell In!'
            ]);
        }

    }


    // *********** SELL OUT ****************** //


    // *********** STOCK ****************** //

    // *********** PRICE SASA VS COMPETITOR ****************** //

    public function PriceVsIndex(){
        return view('report.price-vs-competitor');
    }

    public function PriceVsData(){

    
    }

    // *********** AVAILABILITY ****************** //

    public function availabilityIndex(){
        $data['categories'] = Category::get();
        return view('report.availability', $data);
    }

    public function availabilityAreaData(){

        $categories = Category::get();
        $areas = Area::get();

        $data = new Collection();
        foreach ($areas as $area) {
                $item['test0'] = $area->id;
                $item['test1'] = $area->name;
                $x = 2;
            foreach ($categories as $category) {
                $totalProduct = DB::select(
                    "
                    SELECT COUNT(dv.id) as data_count
                    FROM detail_availability dv
                    JOIN availability a ON dv.id_availability = a.id
                    JOIN stores s ON a.id_store = s.id
                    JOIN sub_areas sa ON s.id_subarea = sa.id
                    JOIN areas ar ON sa.id_area = ar.id
                    JOIN products p ON dv.id_product = p.id
                    JOIN sub_categories sc ON p.id_subcategory = sc.id
                    JOIN categories c ON sc.id_category = c.id
                    WHERE c.id = '".$category->id."'
                    AND ar.id = '".$area->id."'
                    ")[0]->data_count * 1;
                $totalProductAvailability = DB::select(
                    "
                    SELECT COUNT(dv.id) as data_count
                    FROM detail_availability dv
                    JOIN availability a ON dv.id_availability = a.id
                    JOIN stores s ON a.id_store = s.id
                    JOIN sub_areas sa ON s.id_subarea = sa.id
                    JOIN areas ar ON sa.id_area = ar.id
                    JOIN products p ON dv.id_product = p.id
                    JOIN sub_categories sc ON p.id_subcategory = sc.id
                    JOIN categories c ON sc.id_category = c.id
                    WHERE c.id = '".$category->id."'
                    AND ar.id = '".$area->id."'
                    AND dv.available = 1
                    ")[0]->data_count * 1;
                // return response()->json(round($totalProductAvailability / $totalProduct, 2) * 100);
                if ($totalProductAvailability == 0) {
                    $total = 0;
                }else{
                    $total = round($totalProductAvailability / $totalProduct, 2) * 100; 
                }
                $item['test'.$x] = $total;
                $x++;
            }
                $data->push($item);
        }
        return Datatables::of($data)->make(true);
        // return response()->json($data);
    }

    public function availabilityAccountData(){

        $categories = Category::get();
        $accounts = Account::get();

        $data = new Collection();
        foreach ($accounts as $account) {
                $item['test0'] = $account->id;
                $item['test1'] = $account->name;
                $x = 2;
            foreach ($categories as $category) {
                $totalProduct = DB::select(
                    "
                    SELECT COUNT(dv.id) as data_count
                    FROM detail_availability dv
                    JOIN availability a ON dv.id_availability = a.id
                    JOIN stores s ON a.id_store = s.id
                    JOIN accounts ac ON s.id_account = ac.id
                    JOIN products p ON dv.id_product = p.id
                    JOIN sub_categories sc ON p.id_subcategory = sc.id
                    JOIN categories c ON sc.id_category = c.id
                    WHERE c.id = '".$category->id."'
                    AND ac.id = '".$account->id."'
                    ")[0]->data_count * 1;
                $totalProductAvailability = DB::select(
                    "
                    SELECT COUNT(dv.id) as data_count
                    FROM detail_availability dv
                    JOIN availability a ON dv.id_availability = a.id
                    JOIN stores s ON a.id_store = s.id
                    JOIN accounts ac ON s.id_account = ac.id
                    JOIN products p ON dv.id_product = p.id
                    JOIN sub_categories sc ON p.id_subcategory = sc.id
                    JOIN categories c ON sc.id_category = c.id
                    WHERE c.id = '".$category->id."'
                    AND ac.id = '".$account->id."'
                    AND dv.available = 1
                    ")[0]->data_count * 1;
                // return response()->json(round($totalProductAvailability / $totalProduct, 2) * 100);
                if ($totalProductAvailability == 0) {
                    $total = 0;
                }else{
                    $total = round($totalProductAvailability / $totalProduct, 2) * 100; 
                }
                $item['test'.$x] = $total;
                $x++;
            }
                $data->push($item);
        }
        return Datatables::of($data)->make(true);
        // return response()->json($data);
    }

    // *********** DISPLAY SHARE ****************** //

    public function displayShareIndex(){
        return view('report.display_share');
    }

    public function displayShareSpgData(){

        $datas = DisplayShare::where('display_shares.deleted_at', null)
                ->join("stores", "display_shares.id_store", "=", "stores.id")
                ->join("employees", "display_shares.id_employee", "=", "employees.id")
                ->select(
                    'display_shares.*',
                    'stores.name1 as store_name',
                    'employees.name as emp_name')
                ->get();
            
            $x = 0;
        foreach($datas as $data)
        {
            $detail_data = DetailDisplayShare::where('detail_display_shares.id_display_share', $data->id)
                                            ->join('categories','detail_display_shares.id_category','categories.id')
                                            ->join('brands','detail_display_shares.id_brand','brands.id')
                                            ->select(
                                                'detail_display_shares.*',
                                                'categories.name as category_name',
                                                'brands.name as brand_name')->get();
            foreach ($detail_data as $detail) {
                $data[$detail->category_name.'-'.$detail->brand_name.'-tier'] = $detail->tier;
                $data[$detail->category_name.'-'.$detail->brand_name.'-depth'] = $detail->depth;
            // if (condition) {
            //     # code...
            // }
                $x++;
                $data['x']=$x;
            }

        }    

        $categories = Category::get();
        $areas = Area::get();

        // return Datatables::of($data)->make(true);
        return response()->json($datas);
    }


    public function displayShareReportAreaData(){

        $mount = Carbon::now();

        $datas = Employee::where('id_position','6')
                        ->join('employee_sub_areas','employees.id','employee_sub_areas.id_employee')
                        ->select('employees.id','employees.name', 'employee_sub_areas.id_subarea as id_sub_area')->get();

        foreach ($datas as $data) {

            $data['store_cover'] = Store::where('id_subarea',$data->id_sub_area)->count();

            $data['store_panel_cover'] = Store::where('id_subarea',$data->id_sub_area)
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $dataActuals = Store::where('stores.id_subarea',$data->id_sub_area)
                                ->join('display_shares','stores.id','display_shares.id_store')
                                ->whereMonth('display_shares.date', $mount->format('m'))
                                ->whereYear('display_shares.date', $mount->format('Y'))
                                ->groupby('display_shares.id_store')
                                ->pluck('display_shares.id');
            $categoryTB = 1;
            $categoryPF = 2;
            $persenTB = 40;
            $persenPF = 40;
            $data['hitTargetTB'] = 0;
            $data['hitTargetPF'] = 0;


            foreach ($dataActuals as $dataActual) {
                $actualDS = DetailDisplayShare::where('detail_display_shares.id_display_share',$dataActual);
                $actualTB = clone $actualDS;
                $actualTotal = $actualTB->where('id_category',$categoryTB)->sum('tier');
                $actualTB = $actualTB->where('id_category',$categoryTB)->first();
                $data['tierTB'] = $actualTB->tier;
                $data['tierSumTB'] = $actualTotal;

                if ($data['tierSumTB'] == 0) {
                    $data['hitTargetTB'] += 0;
                }else{
                    $nilaiActual = round($data['tierTB'] / $data['tierSumTB'] * 100, 2);
                    if ($nilaiActual >= $persenTB) {
                    $data['hitTargetTB'] += 1;
                    } else
                    $data['hitTargetTB'] += 0;
                }

                $actualPF = clone $actualDS;
                $actualTotal = $actualPF->where('id_category',$categoryPF)->sum('tier');
                $actualPF = $actualPF->where('id_category',$categoryPF)->first();
                $data['tierPF'] = $actualPF->tier;
                $data['tierSumPF'] = $actualTotal;

                if ($data['tierSumPF'] == 0) {
                    $data['hitTargetPF'] += 0;
                }else{
                    $nilaiActual = round($data['tierPF'] / $data['tierSumPF'] * 100, 2);
                    if ($nilaiActual >= $persenPF) {
                    $data['hitTargetPF'] += 1;
                    } else
                    $data['hitTargetPF'] += 0;
                }
            }

            if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['achTB'] = round($data['hitTargetTB'] / 1 * 100, 2).'%';
                }else{
                    $data['achTB'] = round($data['hitTargetTB'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['achTB'] = round($data['hitTargetTB'] / $data['store_panel_cover'] * 100, 2).'%';
            
            }if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['achPF'] = round($data['hitTargetPF'] / 1 * 100, 2).'%';
                }else{
                    $data['achPF'] = round($data['hitTargetPF'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['achPF'] = round($data['hitTargetPF'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeSubArea::where('employee_sub_areas.id_employee',$data->id)
                                ->join('sub_areas','employee_sub_areas.id_subarea','sub_areas.id')
                                ->pluck('sub_areas.name')->toArray();
            $data['location'] = implode(", ",$location);
        }
        // return response()->json($datas);
        return Datatables::of($datas)->make(true);

        // return Datatables::of(collect(DB::select($datas)))
        // ->make(true);
    }

    public function displayShareReportSpgData(){

        $mount = Carbon::now();
        $datas = Employee::where('id_position','1')
                        ->select('employees.id','employees.name')->get();
        foreach ($datas as $data) {

            $data['store_cover'] = EmployeeStore::where('id_employee',$data->id)->count();

            $data['store_panel_cover'] = EmployeeStore::where('id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $dataActuals = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('display_shares','employee_stores.id_store','display_shares.id_store')
                                ->whereMonth('display_shares.date', $mount->format('m'))
                                ->whereYear('display_shares.date', $mount->format('Y'))
                                ->groupby('display_shares.id_store')
                                ->pluck('display_shares.id');
            $categoryTB = 1;
            $categoryPF = 2;
            $persenTB = 40;
            $persenPF = 40;
            $data['hitTargetTB'] = 0;
            $data['hitTargetPF'] = 0;


            foreach ($dataActuals as $dataActual) {
                $actualDS = DetailDisplayShare::where('detail_display_shares.id_display_share',$dataActual);
                $actualTB = clone $actualDS;
                $actualTotal = $actualTB->where('id_category',$categoryTB)->sum('tier');
                $actualTB = $actualTB->where('id_category',$categoryTB)->first();
                $data['tierTB'] = $actualTB->tier;
                $data['tierSumTB'] = $actualTotal;

                if ($data['tierSumTB'] == 0) {
                    $data['hitTargetTB'] += 0;
                }else{
                    $nilaiActual = round($data['tierTB'] / $data['tierSumTB'] * 100, 2);
                    if ($nilaiActual >= $persenTB) {
                    $data['hitTargetTB'] += 1;
                    } else
                    $data['hitTargetTB'] += 0;
                }

                $actualPF = clone $actualDS;
                $actualTotal = $actualPF->where('id_category',$categoryPF)->sum('tier');
                $actualPF = $actualPF->where('id_category',$categoryPF)->first();
                $data['tierPF'] = $actualPF->tier;
                $data['tierSumPF'] = $actualTotal;

                if ($data['tierSumPF'] == 0) {
                    $data['hitTargetPF'] += 0;
                }else{
                    $nilaiActual = round($data['tierPF'] / $data['tierSumPF'] * 100, 2);
                    if ($nilaiActual >= $persenPF) {
                    $data['hitTargetPF'] += 1;
                    } else
                    $data['hitTargetPF'] += 0;
                }
            }

            if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['achTB'] = round($data['hitTargetTB'] / 1 * 100, 2).'%';
                }else{
                    $data['achTB'] = round($data['hitTargetTB'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['achTB'] = round($data['hitTargetTB'] / $data['store_panel_cover'] * 100, 2).'%';
            
            }if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['achPF'] = round($data['hitTargetPF'] / 1 * 100, 2).'%';
                }else{
                    $data['achPF'] = round($data['hitTargetPF'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['achPF'] = round($data['hitTargetPF'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->pluck('stores.name1')->toArray();
            $data['location'] = implode(", ",$location);
        }
        // return response()->json($datas);
        return Datatables::of($datas)->make(true);

        // return Datatables::of(collect(DB::select($datas)))
        // ->make(true);
    }

    public function displayShareReportMdData(){

        $mount = Carbon::now();

        $datas = Employee::where('id_position','2')
                        ->select('employees.id','employees.name')->get();

        foreach ($datas as $data) {

            $data['store_cover'] = EmployeeStore::where('id_employee',$data->id)->count();

            $data['store_panel_cover'] = EmployeeStore::where('id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $dataActuals = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('display_shares','employee_stores.id_store','display_shares.id_store')
                                ->whereMonth('display_shares.date', $mount->format('m'))
                                ->whereYear('display_shares.date', $mount->format('Y'))
                                ->groupby('display_shares.id_store')
                                ->pluck('display_shares.id');
            $categoryTB = 1;
            $categoryPF = 2;
            $persenTB = 40;
            $persenPF = 40;
            $data['hitTargetTB'] = 0;
            $data['hitTargetPF'] = 0;


            foreach ($dataActuals as $dataActual) {
                $actualDS = DetailDisplayShare::where('detail_display_shares.id_display_share',$dataActual);
                $actualTB = clone $actualDS;
                $actualTotal = $actualTB->where('id_category',$categoryTB)->sum('tier');
                $actualTB = $actualTB->where('id_category',$categoryTB)->first();
                $data['tierTB'] = $actualTB->tier;
                $data['tierSumTB'] = $actualTotal;

                if ($data['tierSumTB'] == 0) {
                    $data['hitTargetTB'] += 0;
                }else{
                    $nilaiActual = round($data['tierTB'] / $data['tierSumTB'] * 100, 2);
                    if ($nilaiActual >= $persenTB) {
                    $data['hitTargetTB'] += 1;
                    } else
                    $data['hitTargetTB'] += 0;
                }

                $actualPF = clone $actualDS;
                $actualTotal = $actualPF->where('id_category',$categoryPF)->sum('tier');
                $actualPF = $actualPF->where('id_category',$categoryPF)->first();
                $data['tierPF'] = $actualPF->tier;
                $data['tierSumPF'] = $actualTotal;

                if ($data['tierSumPF'] == 0) {
                    $data['hitTargetPF'] += 0;
                }else{
                    $nilaiActual = round($data['tierPF'] / $data['tierSumPF'] * 100, 2);
                    if ($nilaiActual >= $persenPF) {
                    $data['hitTargetPF'] += 1;
                    } else
                    $data['hitTargetPF'] += 0;
                }
            }

            if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['achTB'] = round($data['hitTargetTB'] / 1 * 100, 2).'%';
                }else{
                    $data['achTB'] = round($data['hitTargetTB'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['achTB'] = round($data['hitTargetTB'] / $data['store_panel_cover'] * 100, 2).'%';
            
            }if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['achPF'] = round($data['hitTargetPF'] / 1 * 100, 2).'%';
                }else{
                    $data['achPF'] = round($data['hitTargetPF'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['achPF'] = round($data['hitTargetPF'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->count() .' STORE';
            $data['location'] = $location;
        }
        // return response()->json($datas);
        return Datatables::of($datas)->make(true);

        // return Datatables::of(collect(DB::select($datas)))
        // ->make(true);
    }

    // *********** ADDITIONAL DISPLAY ****************** //


    public function additionalDisplayIndex(){
        return view('report.additional-display');
    }

    public function additionalDisplaySpgData(){

        $datas = AdditionalDisplay::where('additional_displays.deleted_at', null)
                ->join("stores", "additional_displays.id_store", "=", "stores.id")
                ->join('sub_areas', 'stores.id_subarea', 'sub_areas.id')
                ->join('areas', 'sub_areas.id_area', 'areas.id')
                ->join('regions', 'areas.id_region', 'regions.id')
                ->leftjoin('employee_sub_areas', 'stores.id', 'employee_sub_areas.id_subarea')
                ->leftjoin('employees as empl_tl', 'employee_sub_areas.id_employee', 'empl_tl.id')
                ->join("employees", "additional_displays.id_employee", "=", "employees.id")
                ->leftjoin("detail_additional_displays", "additional_displays.id", "=", "detail_additional_displays.id_additional_display")
                ->join("jenis_displays", "detail_additional_displays.id_jenis_display", "=", "jenis_displays.id")
                ->select(
                    'additional_displays.*',
                    'stores.name1 as store_name',
                    'employees.name as emp_name',
                    'jenis_displays.name as jenis_display_name',
                    'detail_additional_displays.jumlah as jumlah_add',
                    'detail_additional_displays.foto_additional as foto_Add',
                    'regions.name as region_name',
                    'areas.name as area_name',
                    'empl_tl.name as tl_name',
                    'employees.status as jabatan'
                    )
                ->get();
            
        //     $x = 0;
        // foreach($datas as $data)
        // {
        //     $detail_data = DetailAdditionalDisplay::where('detail_additional_displays.id_display_share', $data->id)
        //                                     ->join('categories','detail_additional_displays.id_category','categories.id')
        //                                     ->join('brands','detail_additional_displays.id_brand','brands.id')
        //                                     ->select(
        //                                         'detail_additional_displays.*',
        //                                         'categories.name as category_name',
        //                                         'brands.name as brand_name')->get();
        //     foreach ($detail_data as $detail) {
        //         $data[$detail->category_name.'-'.$detail->brand_name.'-tier'] = $detail->tier;
        //         $data[$detail->category_name.'-'.$detail->brand_name.'-depth'] = $detail->depth;
        //     // if (condition) {
        //     //     # code...
        //     // }
        //         $x++;
        //         $data['x']=$x;
        //     }

        // }    

        $categories = Category::get();
        $areas = Area::get();

        return Datatables::of($datas)->make(true);
        return response()->json($datas);
    }


    public function additionalDisplayReportIndex(){
        return view('report.additional-display');
    }

    public function additionalDisplayReportAreaData(){

        $mount = Carbon::now();

        $datas = Employee::where('id_position','6')
                        ->join('employee_sub_areas','employees.id','employee_sub_areas.id_employee')
                        // ->groupby('employees.id')
                        ->select('employees.id','employees.name', 'employee_sub_areas.id_subarea as id_sub_area')->get();

        foreach ($datas as $data) {

            $data['store_cover'] = Store::where('id_subarea',$data->id_sub_area)->count();

            $data['store_panel_cover'] = Store::where('id_subarea',$data->id_sub_area)
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $data['actual'] = Store::where('stores.id_subarea',$data->id_sub_area)
                                ->join('additional_displays','stores.id','additional_displays.id_store')
                                ->whereMonth('additional_displays.date', $mount->format('m'))
                                ->whereYear('additional_displays.date', $mount->format('Y'))
                                ->groupby('additional_displays.id_store')
                                ->get()
                                ->count();

            if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['ach'] = round($data['actual'] / 1 * 100, 2).'%';
                }
                $data['ach'] = round($data['actual'] / $data['store_cover'] * 100, 2).'%';
            }else{
                $data['ach'] = round($data['actual'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeSubArea::where('employee_sub_areas.id_employee',$data->id)
                                ->join('sub_areas','employee_sub_areas.id_subarea','sub_areas.id')
                                ->pluck('sub_areas.name')->toArray();
            $data['location'] = implode(", ",$location);

        }
        // return response()->json($datas);
        return Datatables::of($datas)->make(true);

        // return Datatables::of(collect(DB::select($datas)))
        // ->make(true);

    }

    public function additionalDisplayReportSpgData(){

        $mount = Carbon::now();

        $datas = Employee::where('id_position','1')
                        // ->rightjoin('employee_stores','employees.id','employee_stores.id_employee')
                        // ->groupby('employees.id')
                        ->select('employees.id','employees.name')->get();
        foreach ($datas as $data) {

            $data['store_cover'] = EmployeeStore::where('id_employee',$data->id)->count();

            $data['store_panel_cover'] = EmployeeStore::where('id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $data['actual'] = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('additional_displays','employee_stores.id_store','additional_displays.id_store')
                                ->whereMonth('additional_displays.date', $mount->format('m'))
                                ->whereYear('additional_displays.date', $mount->format('Y'))
                                ->groupby('additional_displays.id_store')
                                ->get()
                                ->count();

            if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['ach'] = round($data['actual'] / 1 * 100, 2).'%';
                }else{
                    $data['ach'] = round($data['actual'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['ach'] = round($data['actual'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->pluck('stores.name1')->toArray();
            $data['location'] = implode(", ",$location);
        }
        // return response()->json($datas);
        return Datatables::of($datas)->make(true);

        // return Datatables::of(collect(DB::select($datas)))
        // ->make(true);

    }


    public function additionalDisplayReportMdData(){
        $mount = Carbon::now();

        $datas = Employee::where('id_position','2')
                        // ->rightjoin('employee_stores','employees.id','employee_stores.id_employee')
                        // ->groupby('employees.id')
                        ->select('employees.id','employees.name')->get();

        foreach ($datas as $data) {

            $data['store_cover'] = EmployeeStore::where('id_employee',$data->id)->count();

            $data['store_panel_cover'] = EmployeeStore::where('id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $data['actual'] = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('additional_displays','employee_stores.id_store','additional_displays.id_store')
                                ->whereMonth('additional_displays.date', $mount->format('m'))
                                ->whereYear('additional_displays.date', $mount->format('Y'))
                                ->groupby('additional_displays.id_store')
                                ->get()
                                ->count();

            if ($data['store_panel_cover'] == 0) {
                if ($data['store_cover'] == 0) {
                    $data['ach'] = round($data['actual'] / 1 * 100, 2).'%';
                }else{
                    $data['ach'] = round($data['actual'] / $data['store_cover'] * 100, 2).'%';
                }
            }else{
                $data['ach'] = round($data['actual'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->count() .' STORE';
            $data['location'] = $location;
        }
        // return response()->json($datas);
        return Datatables::of($datas)->make(true);

        // return Datatables::of(collect(DB::select($datas)))
        // ->make(true);

    }
}
