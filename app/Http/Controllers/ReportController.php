<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailIn;
use App\SellIn;
use App\SellInSummary;
use App\SalesMtcSummary;
use App\MtcReportTemplate;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use App\Category;
use App\Area;
use App\Account;
use App\DisplayShare;
use App\DetailDisplayShare;
use App\AdditionalDisplay;
use App\DetailAdditionalDisplay;
use Auth;
use DB;
use App\StoreDistributor;
use App\Employee;
use App\EmployeePasar;
use App\Distributor;
use App\Filters\SummaryFilters;
use App\Helper\ReportHelper as ReportHelper;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Sales;
use App\DetailSales;
use App\Target;
use App\StockMdHeader as StockMD;
use App\Outlet;
use App\Attendance;
use App\AttendanceOutlet;
use App\Distribution;
use App\DistributionDetail;
use App\SalesMd as SalesMD;
use App\JobTrace;
use App\Jobs\ExportJob;
use App\Product;

class ReportController extends Controller
{
    protected $reportHelper;

    public function __construct(ReportHelper $reportHelper)
    {
        $this->reportHelper = $reportHelper;
    }

    // *********** SELL IN ****************** //

	public function sellInIndex(){
		return view('report.sellin');
	}

    public function sellInData(SummaryFilters $filters){

        $data = SellInSummary::where('id', '>', 0);

        return Datatables::of($data)
            ->addColumn('action', function ($item) {
                $data = array(
                    'id'            => $item->id,
                    'qty'           => $item->qty
                );

                return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
                <button data-url=".route('sellin.delete', $item->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
                ";
            })->make(true);

    }

    public function sellInDataRaw(Request $request){

        $data = DetailIn::where('deleted_at', null);

        return Datatables::of($data)
        ->addColumn('week', function($item) {
            return $item->sellin->week;
        })        
        ->addColumn('distributor_code', function($item) {
            return $item->sellin->store->getDistributorCode();
        })
        ->addColumn('distributor_name', function($item) {
            return $item->sellin->store->getDistributorName();
        })
        ->addColumn('region', function($item) {
            return $item->sellin->store->subarea->area->region->name;
        })
        ->addColumn('area', function($item) {
            return $item->sellin->store->subarea->area->name;
        })
        ->addColumn('sub_area', function($item) {
            return $item->sellin->store->subarea->name;
        })
        ->addColumn('account', function($item) {
            return $item->sellin->store->account->name;
        })
        ->addColumn('channel', function($item) {
            return $item->sellin->store->account->channel->name;
        })
        ->addColumn('store_name_1', function($item) {
            return $item->sellin->store->name1;
        })
        ->addColumn('store_name_2', function($item) {
            return $item->sellin->store->name2;
        })
        ->addColumn('nik', function($item) {
            return $item->sellin->employee->nik;
        })
        ->addColumn('employee_name', function($item) {
            return $item->sellin->employee->name;
        })
        ->addColumn('date', function($item) {
            return $item->sellin->date;
        })
        ->addColumn('product_name', function($item) {
            return $item->product->name;
        })
        ->addColumn('category', function($item) {
            // return $item->product->category->name;
        })
        ->addColumn('unit_price', function($item) {
            // return $item->product->getPrice(
            //                             $item->sellin->date,
            //                             $item->sellin->store->type,
            //                             ($item->company->typePrice == 2) ? 3 : 1
            //                            );
        })
        ->addColumn('value', function($item) {
            // return  $item->product->getPrice(
                    //                     $item->sellin->date,
                    //                     $item->sellin->store->type,
                    //                     ($item->company->typePrice == 2) ? 3 : 1
                    //                    )
                    // *
                    // $item->isTarget()['target'];
        })
        ->addColumn('value_pf', function($item) {
            // return  $item->product->getPrice(
            //                             $item->sellin->date,
            //                             $item->sellin->store->type,
            //                             ($item->company->typePrice == 2) ? 3 : 1
            //                            )
            //         *
            //         $item->isTarget()['target_pf']
            //         *
            //         $item->isPf();
        })
        ->addColumn('spv_name', function($item) {
            // return $item->sellin->employee->getSpvName();
        })
        ->addColumn('action', function ($item) {
            $data = array(
                'id'            => $item->id,
                'qty'           => $item->qty
            );

            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('sellin.delete', $item->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
            ";
        })->make(true);

    }

	public function sellInDataOld(){

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


    // *********** SALES MTC ****************** //

    public function salesMtcIndex(){
        return view('report.salesmtc');
    }

    public function salesMtcDataSales(SummaryFilters $filters){

        // $data = new SalesMtcSummary('sales_mtc_summary_by_sales');
        $data = SalesMtcSummary::filter($filters);
        
        return Datatables::of($data)
        // ->addColumn('periode', function($item) {
        //     return $item->getSummary('periode');
        // })
        // ->addColumn('region', function($item) {
        //     return $item->getSummary('region');
        // })
        // ->addColumn('is_jawa', function($item) {
        //     return $item->getSummary('is_jawa');
        // })
        // ->addColumn('jabatan', function($item) {
        //     return $item->getSummary('jabatan');
        // })
        // ->addColumn('employee_name', function($item) {
        //     return $item->getSummary('employee_name');
        // })
        // ->addColumn('area', function($item) {
        //     return $item->getSummary('area');
        // })
        // ->addColumn('sub_area', function($item) {
        //     return $item->getSummary('sub_area');
        // })
        // ->addColumn('store_name', function($item) {
        //     return $item->getSummary('store_name');
        // })
        // ->addColumn('account', function($item) {
        //     return $item->getSummary('account');
        // })
        // ->addColumn('category', function($item) {
        //     return $item->getSummary('category');
        // })
        // ->addColumn('product_line', function($item) {
        //     return $item->getSummary('product_line');
        // })
        // ->addColumn('product_name', function($item) {
        //     return $item->getSummary('product_name');
        // })
        // ->addColumn('actual_out_qty', function($item) {
        //     return $item->getSummary('actual_out_qty');
        // })
        // ->addColumn('actual_in_qty', function($item) {
        //     return $item->getSummary('actual_in_qty');
        // })
        // ->addColumn('price', function($item) {
        //     return $item->getSummary('price');
        // })
        // ->addColumn('actual_out_value', function($item) {
        //     return $item->getSummary('actual_out_value');
        // })
        // ->addColumn('actual_in_value', function($item) {
        //     return $item->getSummary('actual_in_value');
        // })
        // ->addColumn('total_actual', function($item) {
        //     return $item->getSummary('total_actual');
        // })
        // ->addColumn('target_qty', function($item) {
        //     return $item->getSummary('target_qty');
        // })
        // ->addColumn('target_value', function($item) {
        //     return $item->getSummary('target_value');
        // })
        ->make(true);
    }

    public function salesMtcDataSalesAlt(SummaryFilters $filters){

        $data = MtcReportTemplate::filter($filters);
        
        $dt = Datatables::of($data);

        foreach ($this->reportHelper->generateColumnSalesMtc() as $column) {
            $dt->addColumn($column, function($item) use ($column) {
                return $item->getSummary($column);
            });
        }

        return $dt->make(true);        
    }

    public function salesMtcDataTarget(SummaryFilters $filters){
        return Datatables::of(SalesMtcSummary('sales_mtc_summary_by_target')->filter($filters))->make(true);
    }


    // *********** STOCK ****************** //


    // *********** EXPORTING ****************** //

    public function tes(){

        $list = collect([
            [ 'id' => 1, 'name' => 'Jane' ],
            [ 'id' => 2, 'name' => 'John' ],
        ]);

        $data = MtcReportTemplate::where('id', '!=', 0);

        $sql = $data->toSql();
        $bindings = $data->getBindings();

        $sql = $data->toSql();
        $bindings = $data->getBindings();

        $data3 = collect(DB::select($sql, $bindings));

        return $this->reportHelper->exportSalesMtc($data);

        // return (new FastExcel($this->reportHelper->mapForExportSalesMtc($data)))->download('file.xlsx');

        // return redirect()->back();

    }

    public function export(Request $request, SummaryFilters $filters){

        // $req = new Request($request->all());
        // return response()->json(asset('..\storage')); 
        // $excel = $this->reportHelper->exporting($request);

        $result = DB::transaction(function () use ($request) {

            try{
                
                // JOB TRACING AND QUEUE
                $trace = JobTrace::create([
                        'id_user' => Auth::user()->id,
                        'date' => Carbon::now(),
                        'title' => $this->reportHelper->getTitle($request),
                        'status' => 'PROCESSING',
                    ]);

                dispatch(new ExportJob($trace, $request->all(), Auth::user()));
                return true;
                return 'Export succeed, please go to download page';       
            }catch(\Exception $e){
                DB::rollback();
                return false;
                return 'Export request failed '.$e->getMessage();
            }

        });

        return response()->json(['result' => $result]);

    }

    public function exportOld(Request $request, SummaryFilters $filters){

        // $list = collect([
        //     [ 'id' => 1, 'name' => 'Jane' ],
        //     [ 'id' => 2, 'name' => 'John' ],
        // ]);

        // (new FastExcel($list))->export('file.xlsx');

        // return;

        // return response()->json(['name' => 'New.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

        // return response()->json($request->all());

        // return response()->json($this->reportHelper->getModel($request));

        // $filterA = new SummaryFilters($request);

        // return response()->json(SellInSummary::filter($filterA)->get());

        // $filename = 'Philips Retail Report Sell Thru ' . Carbon::now()->format('d-m-Y');

        // return $this->reportHelper->exportSalesMtc($filters);

        $data = SellInSummary::filter($filters);

        $sql = $data->toSql();
        $bindings = $data->getBindings();

        // $data2 = DB::select($data)->setBindings($bindings);

        // $data2 = SellInSummary::select(DB::raw($data->toSql()), $bindings);

        $data3 = collect(DB::select($sql, $bindings));

        $dataNew = DB::select($sql, $bindings);

        $data4 = SellInSummary::filter($filters)->get();

        $arr = json_decode(json_encode($data3), TRUE);

        // return response()->json($arr);

        // return response()->json($data->limit(20)->get());

        // $data = $this->reportHelper->getModel($request);

        $filename = 'TEST REPORT '.rand(1000, 10000);
        // $data = SellInSummary::filter(new SummaryFilters($request));

        $excel = Excel::create($filename, function($excel) use ($arr) {

            // Set the title
            $excel->setTitle('Report Sell Thru');

            // Chain the setters
            $excel->setCreator('Philips')
                  ->setCompany('Philips');

            // Call them separately
            $excel->setDescription('Sell Thru Data Reporting');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('SELL THRU', function ($sheet) use ($arr) {
                $sheet->setAutoFilter('A1:S1');
                $sheet->setHeight(1, 25);
                // $sheet->fromModel($this->reportHelper->mapForExportSalesNew($data3), null, 'A1', true, true);
                // $sheet->fromArray([['AAAA', 'BBBB', 'CCCC'],['AAAA', 'BBBB', 'CCCC']], null, 'A2', true, true);
                $sheet->fromArray($arr, null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:S1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:S1', 'thin');
            });


        })->string('xlsx');

        // $model = $this->reportHelper->getModel($request);
        // $excel = $this->reportHelper->exporting($model);

        // return response()->json(['name' => $model['filename'].'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

        return response()->json(['name' => $filename.'.xlsx', 'file' => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'.base64_encode($excel)]);

    }

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

    // ************ SMD PASAR ************ //
    public function SMDpasar()
    {
        $employeePasar = EmployeePasar::with([
            'employee','pasar','pasar.subarea.area',
            'employee.position'
        ])->select('employee_pasars.*');
        $report = array();
        $id = 1;
        for ($i=1; $i <= Carbon::now()->day ; $i++) {
            foreach ($employeePasar->get() as $data) {
                if ($data->employee->position->level == 'mdgtc') {
                    $report[] = array(
                        'id' => $id++,
                        'id_ep' => $data->id,
                        'id_emp' => $data->employee->id,
                        'id_pasar' => $data->pasar->id,
                        'area' => $data->pasar->subarea->area->name,
                        'nama' => $data->employee->name,
                        'jabatan' => $data->employee->position->name,
                        'pasar' =>   $data->pasar->name,
                        'stockist' => $this->getStockist($data, $i),
                        'bulan' => Carbon::now()->month,
                        'tanggal' => $i,
                        'call' => $this->getCall($data, $i),
                        'ro' => $this->getRo($data, $i),
                        'cbd' => $this->getCbd($data, $i),
                    );
                }
            }
        }
        $dt = Datatables::of(collect($report));
        foreach (\App\SubCategory::get() as $cat) {
            $dt->addColumn('cat-'.$cat->id, function($report) use ($cat){
                $date = Carbon::now()->format('Y')."-".$report['bulan']."-".$report['tanggal'];
                $getVal = DB::table('distribution_details')
                ->join('products', 'distribution_details.id_product', '=', 'products.id')
                ->join('distributions', 'distribution_details.id_distribution', '=', 'distributions.id')
                ->whereDate('distribution_details.created_at', '=', Carbon::parse($date))
                ->where([
                    'products.id_subcategory' => $cat->id,
                    'distributions.id_employee' => $report['id_emp']
                ])->count();
                return $getVal;
            });
        }
        foreach (\App\Product::get() as $product) {
            $dt->addColumn('product-'.$product->id, function($report) use ($product){
                // $date = Carbon::now()->format('Y')."-".$report['bulan']."-16";
                $date = Carbon::now()->format('Y')."-".$report['bulan']."-".$report['tanggal'];
                $getOos = DB::table('stock_md_headers')
                ->join('stock_md_details', 'stock_md_headers.id', '=', 'stock_md_details.id_stock')
                ->where([
                    'stock_md_headers.id_employee' => $report['id_emp'],
                    'stock_md_headers.id_pasar' => $report['id_pasar'],
                    'stock_md_headers.date' => $date,
                    'stock_md_details.id_product' => $product->id
                ])->first();
                return (isset($getOos->oos) ? $getOos->oos : "-");
            });
        }
        $dt->addColumn('ec', function($report){
            $date = Carbon::now()->format('Y')."-".$report['bulan']."-".$report['tanggal'];
            return SalesMD::whereDate('date', $date)->count();
        });
        $dt->addColumn('vpf', function($report) {
            $date = Carbon::now()->format('Y')."-".$report['bulan']."-".$report['tanggal'];
            // $date = Carbon::now()->format('Y')."-".$report['bulan']."-15";
            $sale = DB::table('sales_mds')
            ->join('sales_md_details', 'sales_mds.id', '=', 'sales_md_details.id_sales')
            ->join('products', 'sales_md_details.id_product', '=', 'products.id')
            ->join('prices', 'products.id', '=', 'prices.id_product')
            ->whereDate('sales_mds.date', '=', Carbon::parse($date))
            ->where([
                'sales_md_details.is_pf' => 1,
                // 'sales_mds.id_employee' => 101
                'sales_mds.id_employee' => $report['id_emp']
            ])
            ->where('prices.rilis', '<=', $date)
            ->get([
                'sales_mds.id_outlet',
                'sales_md_details.qty_actual',
                'sales_md_details.id_product',
                'prices.price',
            ]);
            // dd($report);
            // dd($sale);
            $getVal = array();
            foreach ($sale as $data) {
                $getVal[] = $data->price*$data->qty_actual;
            }
            return "Rp.".(array_sum($getVal) == 0 ? "-" : array_sum($getVal));
        });
        $dt->addColumn('vnpf', function($report) {
            $date = Carbon::now()->format('Y')."-".$report['bulan']."-".$report['tanggal'];
            // $date = Carbon::now()->format('Y')."-".$report['bulan']."-15";
            $sale = DB::table('sales_mds')
            ->join('sales_md_details', 'sales_mds.id', '=', 'sales_md_details.id_sales')
            ->join('products', 'sales_md_details.id_product', '=', 'products.id')
            ->join('prices', 'products.id', '=', 'prices.id_product')
            ->whereDate('sales_mds.date', '=', Carbon::parse($date))
            ->where([
                'sales_md_details.is_pf' => 0,
                // 'sales_mds.id_employee' => 101
                'sales_mds.id_employee' => $report['id_emp']
            ])
            ->where('prices.rilis', '<=', $date)
            ->get([
                'sales_mds.id_outlet',
                'sales_md_details.qty_actual',
                'sales_md_details.id_product',
                'prices.price',
            ]);
            // dd($report);
            // dd($sale);
            $getVal = array();
            foreach ($sale as $data) {
                $getVal[] = $data->price*$data->qty_actual;
            }
            return "Rp.".(array_sum($getVal) == 0 ? "-" : array_sum($getVal));
        });
        $dt->addColumn('vt', function($report) {
            $date = Carbon::now()->format('Y')."-".$report['bulan']."-".$report['tanggal'];
            $vpf = DB::table('sales_mds')
            ->join('sales_md_details', 'sales_mds.id', '=', 'sales_md_details.id_sales')
            ->join('products', 'sales_md_details.id_product', '=', 'products.id')
            ->join('prices', 'products.id', '=', 'prices.id_product')
            ->whereDate('sales_mds.date', '=', Carbon::parse($date))
            ->where([
                'sales_md_details.is_pf' => 1,
                // 'sales_mds.id_employee' => 101
                'sales_mds.id_employee' => $report['id_emp']
            ])
            ->where('prices.rilis', '<=', $date)
            ->get([
                'sales_mds.id_outlet',
                'sales_md_details.qty_actual',
                'sales_md_details.id_product',
                'prices.price',
            ]);
            $vnpf = DB::table('sales_mds')
            ->join('sales_md_details', 'sales_mds.id', '=', 'sales_md_details.id_sales')
            ->join('products', 'sales_md_details.id_product', '=', 'products.id')
            ->join('prices', 'products.id', '=', 'prices.id_product')
            ->whereDate('sales_mds.date', '=', Carbon::parse($date))
            ->where([
                'sales_md_details.is_pf' => 0,
                // 'sales_mds.id_employee' => 101
                'sales_mds.id_employee' => $report['id_emp']
            ])
            ->where('prices.rilis', '<=', $date)
            ->get([
                'sales_mds.id_outlet',
                'sales_md_details.qty_actual',
                'sales_md_details.id_product',
                'prices.price',
            ]);
            // dd($report);
            // dd($sale);
            $getVpf = array();
            $getVnpf = array();
            foreach ($vpf as $data) {
                $getVpf[] = $data->price*$data->qty_actual;
            }
            foreach ($vnpf as $data) {
                $getVnpf[] = $data->price*$data->qty_actual;
            }
            $total = array_sum($getVpf)+array_sum($getVnpf);
            return "Rp.".($total == 0 ? "-" : $total);
        });
        return $dt->make(true);
    }

    public function SMDattendance()
    {
        $employee = AttendanceOutlet::whereMonth('checkin', Carbon::now()->month)->get();
        // $employee = Employee::where('id_position', \App\Position::where('level', 'mdgtc')->first()->id)
        // ->with('employeePasar')
        // ->select('employees.*')
        // ->get();
        $data = array();
        $absen = array();
        $id = 1;
        foreach ($employee as $val) {
            $data[] = array(
                'id' => $id++,
                'region' => $val->outlet->employeePasar->pasar->name,
                'area' => $val->outlet->employeePasar->pasar->subarea->area->name,
                'subarea' => $val->outlet->employeePasar->pasar->subarea->name,
                'nama' => $val->attendance->employee->name,
                'jabatan' => $val->attendance->employee->position->name,
                'pasar' => $val->outlet->employeePasar->pasar->name,
                'outlet' => $val->outlet->name,
                'tanggal' => Carbon::parse($val->checkin)->day,
                'checkin' => Carbon::parse($val->checkin)->format('H:m:s'),
                'checkout' => ($val->checkout ? Carbon::parse($val->checkout)->format('H:m:s') : "Belum Check-out")
            );
        }
        // foreach ($employee as $value) {
        //     if (isset($value->employeePasar)) {
        //         foreach ($value->employeePasar as $key => $val) {
        //             for ($i=1; $i <= Carbon::now()->endOfMonth()->day ; $i++) {
        //                 $check = Attendance::where('id_employee', $value->id)
        //                 ->whereMonth('date', Carbon::now()->month)
        //                 ->whereDay('date', $i);
        //                 $absen[$key][] = "<td class='".($check->count() > 0 ? ($check->first()->keterangan == "Check-in" ? "bg-success text-white" : ($check->first()->keterangan == "Cuti" || $check->first()->keterangan == "Off" || $check->first()->keterangan == "Sakit" ? "bg-warning text-white" : "" ) ) : "")."'>".$i."</td>";
        //             }
        //             $data[] = array(
        //                 'id' => $id++,
        //                 'region' => $val->pasar->name,
        //                 'area' => $val->pasar->subarea->area->name,
        //                 'subarea' => $val->pasar->subarea->name,
        //                 'nama' => $value->name,
        //                 'jabatan' => $value->position->name,
        //                 'pasar' => $val->pasar->name,
        //                 'bulan' => implode(" ", $absen[$key]),
        //             );
        //         }
        //     }
        // }
        // dd($data);
        return Datatables::of(collect($data))->make(true);
        // return Datatables::of(collect($data))
        // ->addColumn('action', function ($data) {
        //     $html = "<table class='table table-bordered'><tr>";
        //     $html .= "<td class='bg-gd-cherry text-white'>".Carbon::now()->format('F')."</td>";
        //     $html .= $data['bulan'];
        //     $html .= "</tr></table>";
        //     return $html;
        // })->make(true);
    }

    public function SMDdistpf()
    {
        $dist = Distribution::whereMonth('date',Carbon::now()->month)->get();
        $data = array();
        $product = array();
        $id = 1;
        foreach ($dist as $key => $value) {
            $data[] = array(
                'id' => $id++,
                'nama' => $value->employee->name,
                'pasar' => $value->outlet->employeePasar->pasar->name,
                'tanggal' => Carbon::parse($value->date)->day,
                'outlet' => $value->outlet->name
            );
        }
        $dt = Datatables::of(collect($data));
        foreach (Product::get() as $pdct) {
            $dt->addColumn('product-'.$pdct->id, function($dist) use ($pdct) {
                // dd($pdct->id);
                $distribution = DistributionDetail::where([
                    'id_distribution' => $dist['id'],
                    'id_product' => $pdct->id
                ])->first();
                return $distribution['value'];
            });
        }
        return $dt->make(true);
    }

    public function SMDsales()
    {
        $sales = SalesMD::whereMonth('date', Carbon::now()->month)->get();
        $data = array();
        $id = 1;
        foreach ($sales as $value) {
            $data[] = array(
                'id' => $id++,
                'nama' => $sales->employee->name,
                'pasar' => $sales->outlet->employeePasar->pasar->name,
                'tanggal' => $sales->date,
                'outlet' => $sales->outlet->name,
            );
        }
        return Datatables::of(collect($data))->make(true);
    }


    public function getCbd($data, $day)
    {
        $date = Carbon::now()->format('Y-m-').$day;
        $cbd = \App\Cbd::where([
            'id_employee' => $data['id_employee'],
            'date' => $date
        ])->count();
        return $cbd;
    }

    public function getStockist($data, $day)
    {
        $date = Carbon::now()->format('Y-m-').$day;
        $stock = StockMD::where([
            'id_pasar' => $data['id_pasar'],
            'date' => $date
        ])->first();
        return (isset($stock->stockist) ? $stock->stockist : "Tidak ada");
    }

    public function getCall($data, $day)
    {
        $date = Carbon::now()->format('Y-m-').$day;
        $call = DB::table('attendances')
        ->join('attendance_outlets', 'attendances.id', '=', 'attendance_outlets.id_attendance')
        ->where([
            'keterangan' => 'Check-in',
            'id_employee' => $data['id_employee']
        ])
        ->whereRaw("DATE(date) = '".$date."'");
        // $call = Attendance::where([
        //     'keterangan' => 'Check-in',
        //     'id_employee' => $data['id_employee']
        // ])->whereRaw("DATE(date) = '".$date."'")
        // ->with('attendanceOutlet');
        // $call = AttendanceOutlet::with(['attendance' => function($q) use ($data, $date) {
        //     $q->where([
        //         'keterangan' => 'Check-in',
        //         'id_employee' => $data['id_employee']
        //     ])->whereRaw("DATE(date) = '".$date."'");
        // }, 'outlet' => function($q) use ($data) {
        //     $q->where('id_employee_pasar', $data['id']);
        // }]);
        return $call->count();
    }

    public function getRo($data, $day)
    {
        $date = Carbon::now()->format('Y-m-').$day;
        $ro = Outlet::where([
            'id_employee_pasar' => $data['id'],
            'active' => true
        ])->whereRaw("DATE(created_at) > '".$date."'");
        return $ro->count();
    }

    public function getAchievement($date = '')
    {
        $str = 
        "
             SELECT * FROM sales_mtc_summary
             WHERE date between '2018-11-01' and '2018-11-30'
        ";

        return DB::select($str);
        $sales = DetailSales::whereHas('sales', function($query)
        {
            return $query->whereMonth('date', Carbon::now()->month);
        })->limit(50)->get();
        return $sales;
        // $data = array();
        // $id = 1;
        // foreach ($sales as $value) {
        //     $data[] = array(
        //         'id' => $id++,
        //         'nama' => $sales->employee->name,
        //         'pasar' => $sales->outlet->employeePasar->pasar->name,
        //         'tanggal' => $sales->date,
        //         'outlet' => $sales->outlet->name,
        //     );
        // }
        // return Datatables::of(collect($data))->make(true);
    }

}