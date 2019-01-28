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
use App\Components\traits\WeekHelper;
use App\Category;
use App\Area;
use App\SubArea;
use App\Account;
use App\DisplayShare;
use App\DetailAvailability;
use App\DetailDisplayShare;
use App\AdditionalDisplay;
use App\DetailAdditionalDisplay;
use App\DataPrice;
use App\DetailDataPrice;
use App\EmployeeStore;
use App\Store;
use App\EmployeeSubArea;
use App\Brand;
use Auth;
use DB;
use App\Block;
use Excel;
use App\DocumentationDc;
use App\StoreDistributor;
use App\Employee;
use App\EmployeePasar;
use App\Distributor;
use App\Filters\SummaryFilters;
use App\Helper\ReportHelper as ReportHelper;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Sales;
use App\DetailSales;
use App\Target;
use App\StockMdHeader as StockMD;
use App\StockMdDetail;
use App\Outlet;
use App\Attendance;
use App\AttendancePasar;
use App\AttendanceOutlet;
use App\Distribution;
use App\DistributionMotoric;
use App\DistributionDetail;
use App\DistributionMotoricDetail;
use App\SalesMd as SalesMD;
use App\Cbd;
use App\NewCbd;
use App\JobTrace;
use App\Jobs\ExportJob;
use App\Jobs\ExportSPGPasarAchievementJob;
use App\Jobs\ExportSPGPasarSalesSummaryJob;
use App\Jobs\ExportDCReportInventoriJob;
use App\Jobs\ExportSMDReportSalesSummaryJob;
use App\Jobs\ExportSMDReportKPIJob;
use App\Jobs\ExportMTCAchievementJob;
use App\Jobs\ExportGTCCbdJob;
use App\Jobs\ExportMTCDisplayShareJob;
use App\Jobs\ExportMTCAvailabilityJob;
use App\Jobs\ExportMTCDisplayShareAchievementJob;
use App\Jobs\ExportMTCAdditionalDisplayAchievementJob;
use App\Jobs\ExportMTCPriceRowJob;
use App\Jobs\ExportMTCPriceSummaryJob;
use App\Jobs\ExportMTCPriceCompJob;
use App\Jobs\ExportMTCAdditionalDisplayJob;
use App\Product;
use App\ProductCompetitor;
use App\SalesSpgPasar;
use App\SalesMotoricDetail;
use App\SalesMotoric;
use App\AttendanceBlock;
use App\SalesSpgPasarDetail;
use App\SalesRecap;
use App\SalesMdDetail;
use App\SalesDcDetail;
use App\SalesDc;
use App\PlanDc;
use App\PlanEmployee;
use App\SamplingDc;
use App\SamplingDcDetail;
use App\Filters\EmployeeFilters;
use App\Filters\EmployeeStoreFilters;
use App\Filters\SalesSpgSummaryFilters;
use App\Model\Extend\SalesSpgPasarAchievement;
use App\Model\Extend\SalesSpgPasarSummary;
use App\Model\Extend\TargetKpiMd;
use App\SubCategory;
use App\ProductFokusSpg;
use App\ReportInventori;
use App\PropertiDc;
use App\ProductFokus;
use App\FokusProduct;
use App\Model\Extend\SalesMdSummary;
use App\ProductFokusGtc;
use App\Pf;

class ReportController extends Controller
{
    use WeekHelper;
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

    // *********** ACHIEVEMENT **************** //

    public function achievementSalesMtcIndex(){
        return view('report.achievement-salesmtc');
    }

    public function achievementSalesMtcDataSPG(Request $request, EmployeeStoreFilters $filters){

        $periode = Carbon::parse($request->periode);
        $data = EmployeeStore::whereHas('employee.position', function($query){
                    return $query->where('level', 'spgmtc');
                });
                
        /* FILTER */
        if($request->store != null && $request->store != 'null'){
            $data = $data->where('id_store', $request->store);
        }
        if($request->employee != null && $request->employee != 'null'){
            $data = $data->where('id_employee', $request->employee);
        }
        $data = $data->groupBy(['id_employee','id_store'])->orderBy('id_employee', 'ASC');

        // foreach ($data as $item) {
            
        //     $item['employee_name'] = $item->employee->name;
        //     $item['actual_previous'] = number_format($item->employee->getActualPrevious(['store' => $item->id_store, 'date' => $periode]));
        //     $item['actual_current'] = number_format($item->employee->getActual(['store' => $item->id_store, 'date' => $periode]));
        //     $item['target'] = number_format($item->employee->getTarget(['store' => $item->id_store, 'date' => $periode]));
        //     $item['achievement'] = $item->employee->getAchievement(['store' => $item->id_store, 'date' => $periode]);
        //     $item['growth'] = $item->employee->getGrowth(['store' => $item->id_store, 'date' => $periode]);
        //     $item['store_name'] = $item->store->name1;

        // }

        // return response()->json($data->get());

        return Datatables::of($data)        
        ->addColumn('employee_name', function($item) {
            // return $this->getWeek(Carbon::parse('2018-12-31'));
            // return Carbon::now()->day;
            return $item->employee->name;
        })
        ->addColumn('actual_previous', function($item) use ($periode) {
            return number_format($item->employee->getActualPrevious(['store' => $item->id_store, 'date' => $periode]));
        })
        ->addColumn('actual_current', function($item) use ($periode) {
            return number_format($item->employee->getActual(['store' => $item->id_store, 'date' => $periode]));
        })
        ->addColumn('target', function($item) use ($periode) {            
            return number_format($item->employee->getTarget(['store' => $item->id_store, 'date' => $periode]));
        })
        ->addColumn('achievement', function($item) use ($periode) {
            return $item->employee->getAchievement(['store' => $item->id_store, 'date' => $periode]);
        })
        ->addColumn('target_focus1', function($item) use ($periode) {
            return number_format($item->employee->getTarget1Alt(['store' => $item->id_store, 'date' => $periode]));
        })
        ->addColumn('achievement_focus1', function($item) use ($periode) {
            return number_format($item->employee->getActualPf1(['store' => $item->id_store, 'date' => $periode]));
        })
        ->addColumn('percentage_focus1', function($item) use ($periode) {
            return $item->employee->getAchievementPf1(['store' => $item->id_store, 'date' => $periode]);
        })
        ->addColumn('target_focus2', function($item) use ($periode) {
            return number_format($item->employee->getTarget2Alt(['store' => $item->id_store, 'date' => $periode]));
        })
        ->addColumn('achievement_focus2', function($item) use ($periode) {
            return number_format($item->employee->getActualPf2(['store' => $item->id_store, 'date' => $periode]));
        })
        ->addColumn('percentage_focus2', function($item) use ($periode) {
            return $item->employee->getAchievementPf2(['store' => $item->id_store, 'date' => $periode]);
        })
        ->addColumn('growth', function($item) use ($periode) {
            return $item->employee->getGrowth(['store' => $item->id_store, 'date' => $periode]);
        })
        ->addColumn('store_name', function($item) use ($periode) {
            return $item->store->name1;
            return $item->employee->getActualPf1(['id_channel' => $item->store->account->id_channel, 'date' => $periode]);
        })
        ->make(true);     
    }

    public function achievementSalesMtcDataMD(Request $request, EmployeeFilters $filters){

        $periode = Carbon::parse($request->periode);
        $data = Employee::whereHas('position', function ($query){
                    return $query->where('level', 'mdmtc');
                });

        /* FILTER */
        if($request->employee != null && $request->employee != 'null'){
            $data = $data->where('id', $request->employee);
        }
        $data = $data->orderBy('id', 'ASC');

        return Datatables::of($data)        
        ->addColumn('employee_name', function($item) {
            return $item->name;
        })
        ->addColumn('actual_previous', function($item) use ($periode) {
            return number_format($item->getActualPrevious(['date' => $periode]));
        })
        ->addColumn('actual_current', function($item) use ($periode) {
            return number_format($item->getActual(['date' => $periode]));
        })
        ->addColumn('target', function($item) use ($periode) {
            return number_format($item->getTarget(['date' => $periode]));
        })
        ->addColumn('achievement', function($item) use ($periode) {
            return $item->getAchievement(['date' => $periode]);
        })
        ->addColumn('growth', function($item) use ($periode) {
            return $item->getGrowth(['date' => $periode]);
        })
        ->addColumn('target_focus1', function($item) use ($periode) {   
            return number_format($item->getTarget1Alt(['date' => $periode]));
        })
        ->addColumn('achievement_focus1', function($item) use ($periode) {
            return number_format($item->getActualPf1(['date' => $periode]));
        })
        ->addColumn('percentage_focus1', function($item) use ($periode) {
            return $item->getAchievementPf1(['date' => $periode]);
        })
        ->addColumn('target_focus2', function($item) use ($periode) {
            return number_format($item->getTarget2Alt(['date' => $periode]));
        })
        ->addColumn('achievement_focus2', function($item) use ($periode) {
            return number_format($item->getActualPf2(['date' => $periode]));
        })
        ->addColumn('percentage_focus2', function($item) use ($periode) {
            return $item->getAchievementPf2(['date' => $periode]);
        })
        ->addColumn('jml_store', function($item) {
            return $item->employeeStore->count();
        })
        ->make(true);     
    }

    public function achievementSalesMtcDataTL(Request $request, EmployeeStoreFilters $filters){

        $periode = Carbon::parse($request->periode);
        $data = Employee::with('employeeSubArea.subarea')
                ->whereHas('position', function($query){
                    return $query->where('level', 'tlmtc');
                });

        /* FILTER */
        if($request->area != null && $request->area != 'null'){
            $data = $data->whereHas('employeeSubArea.subarea.area', function($query) use ($request){
                                return $query->where('id', $request->area);
                            });
        }
        if($request->employee != null && $request->employee != 'null'){
            $data = $data->where('id', $request->employee);
        }
        $data = $data->orderBy('id', 'ASC');

        return Datatables::of($data)        
        ->addColumn('employee_name', function($item) {
            return $item->name;
        })
        ->addColumn('actual_previous', function($item) use ($periode) {
            return number_format($item->getActualPrevious(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]));
        })
        ->addColumn('actual_current', function($item) use ($periode) {
            return number_format($item->getActual(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]));
        })
        ->addColumn('target', function($item) use ($periode) {
            return number_format($item->getTarget(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]));
        })
        ->addColumn('achievement', function($item) use ($periode) {
            return $item->getAchievement(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]);
        })
        ->addColumn('target_focus1', function($item) use ($periode) {
            return number_format($item->getTarget1Alt(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]));
        })
        ->addColumn('achievement_focus1', function($item) use ($periode) {
            return number_format($item->getActualPf1(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]));
        })
        ->addColumn('percentage_focus1', function($item) use ($periode) {
            return $item->getAchievementPf1(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]);
        })
        ->addColumn('target_focus2', function($item) use ($periode) {
            return number_format($item->getTarget2Alt(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]));
        })
        ->addColumn('achievement_focus2', function($item) use ($periode) {
            return number_format($item->getActualPf2(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]));
        })
        ->addColumn('percentage_focus2', function($item) use ($periode) {
            return $item->getAchievementPf2(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]);
        })
        ->addColumn('growth', function($item) use ($periode) {
            return $item->getGrowth(['sub_area' => @$item->employeeSubArea[0]->subarea->name, 'date' => $periode]);
        })
        ->addColumn('area', function($item) {
            return @$item->employeeSubArea[0]->subarea->area->name;
        })
        ->make(true);     
    }

    public function achievementSalesMtcExportXLS($filterPeriode)
    {
        $result = DB::transaction(function() use ($filterPeriode){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Achievement " . Carbon::parse($filterPeriode)->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCAchievementJob($JobTrace, $filterPeriode, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function cbdGtcExportXLS($filterMonth, $filterYear, $filterEmployee, $filterOutlet, $new = '')
    {
        $filters['month']       = $filterMonth;
        $filters['year']        = $filterYear;
        $filters['employee']    = $filterEmployee;
        $filters['outlet']      = $filterOutlet;
        $filters['new']         = $new;
        
        $result = DB::transaction(function() use ($filters){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "GTC - CBD " . Carbon::parse('1/'.$filters['month'].'/'.$filters['year'])->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportGTCCbdJob($JobTrace, $filters, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
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
        $subCategories = SubCategory::get();
        foreach ($subCategories as $category) {
            $data['products'.$category->id] = Product::where('products.id_subcategory',$category->id)->get();
            $data['productCompetitors'.$category->id] = ProductCompetitor::where('product_competitors.id_subcategory',$category->id)
                                                                            ->join('brands','product_competitors.id_brand','brands.id')
                                                                            ->select('product_competitors.*','brands.name as brand_name')->orderBy('brand_name')->get();
        }
        $data['subCategories'] = $subCategories;
        // return response()->json($data);

        return view('report.price-vs-competitor', $data);
    }

    public function store(Request $request) 
    {
        $data = $request->all();
        // return response()->json($data);

        foreach ($data['products'] as $key => $id_product){
            $product = Product::where('id',$id_product)->first();
                $product->update([
                'id_main_competitor' => $data['competitors'][$key],
                ]);
        }

        return redirect()->back()
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah main competitor!'
        ]);
    }

    public function priceDataVs(Request $request){
        // return response()->json($request);
        
        if (!empty($request->input('periode'))) {
            $date = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $year   = Carbon::now()->format('Y');
            $month  = Carbon::now()->format('m');
        }

        if (!empty($request->input('store'))) {
            $store   = $request->input('store');
        }else{
            $store   = Store::first()->id;
        }
        
        $products = Product::join('brands','products.id_brand','brands.id')
        ->join('sub_categories','products.id_subcategory','sub_categories.id')
        ->join('categories','sub_categories.id_category','categories.id')
        ->select('products.*','brands.name as brand_name','categories.name as category_name')->get();
        foreach ($products as $product) {
            $product['competitor_name'] = '';
            $product['competitor_brand'] = '';
            $product['price'] = '';
            $product['price_competitor'] = '';
            $product['index'] = '';

            $competitors = ProductCompetitor::where('product_competitors.id', $product->id_main_competitor)
            ->join('brands','product_competitors.id_brand','brands.id')
            ->select('product_competitors.*','brands.name as brand_name_competitor')->first();

        // return response()->json($competitors);

            $price = DataPrice::where('data_price.id_store', $store)
                        ->whereMonth('data_price.date', $month)
                        ->whereYear('data_price.date', $year)
                        ->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
                        ->where('detail_data_price.id_product',$product->id)
                        ->where('detail_data_price.isSasa',1)->first();

            if ($price) {
                $product['price'] = $price->price;
            }
            if ($competitors) {
                $product['competitor_name'] = $competitors->name;
                $product['competitor_brand'] = $competitors->brand_name_competitor;
                $priceCompetitor = DataPrice::where('data_price.id_store', $store)
                        ->whereMonth('data_price.date', $month)
                        ->whereYear('data_price.date', $year)
                        ->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
                        ->where('detail_data_price.id_product',$competitors->id)
                        ->where('detail_data_price.isSasa',0)->first();
                if ($priceCompetitor) {
                    $product['price_competitor'] = $priceCompetitor->price;
                    if($product['price']>0){
                        $product['index'] = abs($product['price']-$product['price_competitor']); 
                    }
                }
            }
        }
        // return response()->json($products);
        return Datatables::of($products)->make(true);
    }

    public function priceDataVsExportXLS(Request $request)
    {
        $req['periode'] = ($request->periode == "null" || empty($request->periode) ? null : $request->periode);
        $req['store'] = ($request->store == "null" || empty($request->store) ? 1 : $request->store);
        $req['limitLs'] = ($request->limit == "null" || empty($request->limit) ? null : $request->limit);

        $result = DB::transaction(function() use ($req){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Price VS Competitor (" . ($req['limitLs'] == null ? "All Data" : $req['limitLs'] . " Data") . ") - " . Carbon::now()->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCPriceCompJob($JobTrace, $req, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function priceRow (){
        $account = Account::first()->id;
        $data['stores'] = Store::where('stores.id_account',$account)->orderBy('id_subarea')->get();
        // return response()->json($datas2);
        return view('report.price-row', $data);
    }
    public function priceDataRow(Request $request){
        // return response()->json($request);
        
        if (!empty($request->input('periode'))) {
            $date = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $year   = Carbon::now()->format('Y');
            $month  = Carbon::now()->format('m');
        }

        if (!empty($request->input('account'))) {
            $account   = $request->input('account');
        }else{
            $account   = '1';
        }

        $subareas = SubArea::get();
        $stores = Store::where('stores.id_account',$account)->orderBy('id_subarea')->get();
                // ->pluck('stores.id');

        if ($request->get("storeList") == "yes") return response()->json($stores);

        $datas1 = Product::join('brands','products.id_brand','brands.id')
                        ->join('sub_categories','products.id_subcategory','sub_categories.id')
                        ->join('categories','sub_categories.id_category', 'categories.id')
                        ->select('products.*',
                            'brands.name as brand_name',
                            'categories.name as category_name')
                        ->orderBy('categories.id', 'ASC')->get();

        foreach ($datas1 as $data1) {
            $data1['lowest'] = '';
            $data1['highest'] = '';
            $data1['vs'] = '';

            foreach ($stores as $store ) {
                $data1[$store->name1.'_price'] = '';
                $price = DataPrice::where('data_price.id_store',$store->id)
                            ->whereMonth('data_price.date', $month)
                            ->whereYear('data_price.date', $year)
                            ->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
                            ->where('detail_data_price.id_product',$data1->id)
                            ->where('detail_data_price.isSasa',1)->first();
                                

                if($price){
                $data1[$store->name1.'_price'] = $price->price;

                    if (($data1['lowest'] == '')&&($data1[$store->name1.'_price'] != null)) {
                            $data1['lowest'] = $data1[$store->name1.'_price'];
                            $data1['highest'] = $data1[$store->name1.'_price'];
                        
                    }
                    if(($data1['lowest'] > $data1[$store->name1.'_price'])&&($data1[$store->name1.'_price'] != null)){
                        $data1['lowest'] = $data1[$store->name1.'_price'];
                    }
                    if(($data1['highest'] < $data1[$store->name1.'_price'])&&($data1[$store->name1.'_price'] != null)){
                        $data1['highest'] = $data1[$store->name1.'_price'];
                    }
                }
                }

            // }
            if ($data1['lowest'] != '') {
                $data1['vs'] = round($data1['highest'] / $data1['lowest'] * 1, 2);
            }
        }        


        $datas2 = ProductCompetitor::join('brands','product_competitors.id_brand','brands.id')
                        ->join('sub_categories','product_competitors.id_subcategory','sub_categories.id')
                        ->join('categories','sub_categories.id_category', 'categories.id')
                        ->select('product_competitors.*',
                            'brands.name as brand_name',
                            'categories.name as category_name')
                        ->orderBy('categories.id', 'ASC')->get();


        foreach ($datas2 as $data2) {
            $data2['lowest'] = '';
            $data2['highest'] = '';
            $data2['vs'] = '';

            foreach ($stores as $store ) {
                $data2[$store->name1.'_price'] = '';
                $price = DataPrice::where('data_price.id_store',$store->id)
                            ->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
                            ->where('detail_data_price.id_product',$data2->id)
                            ->where('detail_data_price.isSasa',0)->first();
        // return response()->json($price);
                                

                if($price){
                $data2[$store->name1.'_price'] = $price->price;

                    if (($data2['lowest'] == '')&&($data2[$store->name1.'_price'] != null)) {
                            $data2['lowest'] = $data2[$store->name1.'_price'];
                            $data2['highest'] = $data2[$store->name1.'_price'];
                        
                    }
                    if(($data2['lowest'] > $data2[$store->name1.'_price'])&&($data2[$store->name1.'_price'] != null)){
                        $data2['lowest'] = $data2[$store->name1.'_price'];
                    }
                    if(($data2['highest'] < $data2[$store->name1.'_price'])&&($data2[$store->name1.'_price'] != null)){
                        $data2['highest'] = $data2[$store->name1.'_price'];
                    }
                }

            }
            if ($data2['lowest'] != '') {
                $data2['vs'] = round($data2['highest'] / $data2['lowest'] * 1, 2);
            }
        $merged = $datas1->push($data2); // Contains foo and bar.
        }        

        // return response()->json($merged);
        return Datatables::of($merged)
        ->make(true);
    }

    public function PriceRowExportXLS(Request $request)
    {
        $req['periode'] = ($request->periode == "null" || empty($request->periode) ? null : $request->periode);
        $req['account'] = ($request->account == "null" || empty($request->account) ? 1 : $request->account);
        $req['limitLs'] = ($request->limit == "null" || empty($request->limit) ? null : $request->limit);

        $result = DB::transaction(function() use ($req){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Price ROW (" . ($req['limitLs'] == null ? "All Data" : $req['limitLs'] . " Data") . ") - " . Carbon::now()->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCPriceRowJob($JobTrace, $req, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function priceSummary (){
        $data['accounts'] = Account::get();
        // return response()->json($datas2);
        return view('report.price-summary', $data);
    }

    public function priceDataSummary(Request $request){
        // return response()->json($request);
        
        if (!empty($request->input('periode'))) {
            $date = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $year   = Carbon::now()->format('Y');
            $month  = Carbon::now()->format('m');
        }
        $subareas = SubArea::get();
        $accounts = Account::get();

        $datas2 = Product::join('brands','products.id_brand','brands.id')
                        ->join('sub_categories','products.id_subcategory','sub_categories.id')
                        ->join('categories','sub_categories.id_category', 'categories.id')
                        ->select('products.*',
                            'brands.name as brand_name',
                            'categories.name as category_name')
                        ->orderBy('category_name')->get();

        foreach ($datas2 as $data2) {
            $data2['lowest'] = '';
            $data2['highest'] = '';
            $data2['vs'] = '';

            foreach ($accounts as $account) {
                // $data2[$subarea->id.'_min'] = '-';
                // $data2[$subarea->id.'_max'] = '-';

                $store = Store::where('stores.id_account',$account->id)
                            ->pluck('stores.id');
                $price = DataPrice::whereIn('data_price.id_store',$store)
                                ->whereMonth('data_price.date', $month)
                                ->whereYear('data_price.date', $year)
                                ->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
                                ->where('detail_data_price.id_product',$data2->id)
                                ->where('detail_data_price.isSasa',1);
                if($price){
                    $storeMin = $price->where('price', $price->min('price'))->pluck('id_store');
                    $location = Store::whereIn('stores.id',$storeMin)
                                    ->pluck('stores.name1')->toArray();
                    $data2[$account->id.'store_min'] = implode(", ",$location);

                    $storeMax = $price->where('price', $price->max('price'))->pluck('id_store');
                    $location = Store::whereIn('stores.id',$storeMax)
                                    ->pluck('stores.name1')->toArray();
                    $data2[$account->id.'store_max'] = implode(", ",$location);

                    $data2[$account->id.'_min'] = $price->min('price');
                    $data2[$account->id.'_max'] = $price->max('price');

                    if (($data2['lowest'] == '')&&($data2[$account->id.'_min'] != null)) {
                            $data2['lowest'] = $data2[$account->id.'_min'];
                            $data2['highest'] = $data2[$account->id.'_max'];
                        
                    }
                    if(($data2['lowest'] > $data2[$account->id.'_min'])&&($data2[$account->id.'_min'] != null)){
                        $data2['lowest'] = $data2[$account->id.'_min'];
                    }
                    if(($data2['highest'] < $data2[$account->id.'_max'])&&($data2[$account->id.'_max'] != null)){
                        $data2['highest'] = $data2[$account->id.'_max'];
                    }
                }

            }
            if ($data2['lowest'] != '') {
                $data2['vs'] = round($data2['highest'] / $data2['lowest'] * 1, 2);
            }
        }        

        // return response()->json($datas2);
        return Datatables::of($datas2)->make(true);
    }

    public function PriceSummaryExportXLS(Request $request)
    {
        $req['periode'] = ($request->periode == "null" || empty($request->periode) ? null : $request->periode);
        $req['limitLs'] = ($request->limit == "null" || empty($request->limit) ? null : $request->limit);

        $result = DB::transaction(function() use ($req){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Price Summary (" . ($req['limitLs'] == null ? "All Data" : $req['limitLs'] . " Data") . ") - " . Carbon::now()->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCPriceSummaryJob($JobTrace, $req, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }


    // *********** OOSTOCK ****************** //


    public function oosRow(){
        $data['categories'] = Category::get();
        $data['products'] = Product::get();
        $data['jml_product'] = Product::get()->count();
        $data['categories'] = Category::get();
        $data['brands'] = Brand::get();
        $data['jml_brand'] = Brand::get()->count();
        return view('report.oos', $data);
    }

    public function oosAccountRowData(Request $request){
        // return response()->json($request);

        if (!empty($request->input('account'))) {
            $account   = $request->input('account');
        }else{
            $account   = Account::first()->id;
        }

        $categories = Category::get();

        $totaltanggal = Carbon::now()->daysInMonth;
        // $stores = Store::where('id_account',$account)->get();
        // $datas = new Collection();
        // $i = 1;
        // while ( $i<=$totaltanggal ) {
        //     foreach ($stores as $store) {
        //         $item['date'] = $i;
        //         $item['store'] = $store->name1;
        //         $item['account'] = $store->account->name;
        //         $item['subarea'] = $store->subarea->name;
        //     $datas->push($item);
        //     }
        //     $i++;
        // }

        $datas = Store::where('stores.id_account',$account)
                        ->join('oos','stores.id','oos.id_store')
                        ->join('oos_details','oos.id','oos_details.id_oos')
                        ->leftjoin('accounts','stores.id_account','accounts.id')
                        ->leftjoin('sub_areas','stores.id_subarea','sub_areas.id')
                // ->when($request->has('employee'), function ($q) use ($request){
                //     return $q->where('display_shares.id_employee',$request->input('employee'));
                // })
                ->when($request->has('periode'), function ($q) use ($request){
                    return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
                    ->whereYear('date', substr($request->input('periode'), 3));
                })
                ->when(!empty($request->input('store')), function ($q) use ($request){
                    return $q->where('id_store', $request->input('store'));
                })
                ->when($request->has('area'), function ($q) use ($request){
                    return $q->where('id_area', $request->input('area'));
                })
                ->when($request->has('week'), function ($q) use ($request){
                    return $q->where('oos.week', $request->input('week'));
                })
                        ->select(
                            'stores.id',
                            'oos.date as oos_date',
                            'stores.name1',
                            'stores.name2',
                            'accounts.name as account_name',
                            'sub_areas.name as area_name',
                            'oos_details.id as oos_id'
                            )->orderBy('oos_date')->get();

        foreach($datas as $data) {
                        $data['cek'] = 'NO';
            foreach ($categories as $category) {
                $data[$category->id] = $category->name;
                $data[$category->id.'sum'] = 0;
                $data[$category->id.'sumAvailable'] = 0;
                $products = Product::join('sub_categories','products.id_subcategory','sub_categories.id')
                                ->join('categories','sub_categories.id_category', 'categories.id')
                                ->join('product_stock_types','products.stock_type_id','product_stock_types.id')
                                ->where('categories.id',$category->id)
                                ->select('products.*','product_stock_types.quantity as type_qty')->get();
                foreach ($products as $product) {
                    $data[$category->id.'_'.$product->id] = '-';
                    $detail_data = OosDetail::where('oos_details.id', $data->oos_id)
                                                    ->where('oos_details.id_product',$product->id)
                                                    ->first();
                    if ($detail_data) {
                        if ($detail_data->qty >= $product->type_qty) {
                            $data[$category->id.'_'.$product->id] = 1;
                        }else{
                            $data[$category->id.'_'.$product->id] = 0;
                        }
                        $data[$category->id.'_'.$product->id] = $detail_data->available;
                        $data[$category->id.'sumAvailable'] += $detail_data->available;
                        $data[$category->id.'sum'] += 1;
                        $data['cek'] = 'CEK';
                    }

                }
                    if ($data[$category->id.'sum'] > 0){
                        $data[$category->id.'oos'] = round($data[$category->id.'sumAvailable'] / $data[$category->id.'sum'] * 100, 2).'%';

                    }else{
                        $data[$category->id.'oos'] = 'mobile';
                    }
            }

        } 

        // return response()->json($datas);

        return Datatables::of($datas)->make(true);
        // return response()->json($data);
    }


    // *********** AVAILABILITY ****************** //

    public function availabilityRow(){
        $data['categories'] = Category::get();
        $data['products'] = Product::get();
        $data['jml_product'] = Product::get()->count();
        $data['categories'] = Category::get();
        $data['brands'] = Brand::get();
        $data['jml_brand'] = Brand::get()->count();
        return view('report.availability', $data);
    }

    public function availabilityAccountRowData(Request $request){
        // return response()->json($request);

        if (!empty($request->input('account'))) {
            $account   = $request->input('account');
        }else{
            $account   = Account::first()->id;
        }

        $categories = Category::get();

        $totaltanggal = Carbon::now()->daysInMonth;
        // $stores = Store::where('id_account',$account)->get();
        // $datas = new Collection();
        // $i = 1;
        // while ( $i<=$totaltanggal ) {
        //     foreach ($stores as $store) {
        //         $item['date'] = $i;
        //         $item['store'] = $store->name1;
        //         $item['account'] = $store->account->name;
        //         $item['subarea'] = $store->subarea->name;
        //     $datas->push($item);
        //     }
        //     $i++;
        // }

        $datas = Store::where('stores.id_account',$account)
                        ->join('availability','stores.id','availability.id_store')
                        ->join('detail_availability','availability.id','detail_availability.id_availability')
                        ->leftjoin('accounts','stores.id_account','accounts.id')
                        ->leftjoin('sub_areas','stores.id_subarea','sub_areas.id')
                // ->when($request->has('employee'), function ($q) use ($request){
                //     return $q->where('display_shares.id_employee',$request->input('employee'));
                // })
                ->when($request->has('periode'), function ($q) use ($request){
                    return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
                    ->whereYear('date', substr($request->input('periode'), 3));
                })
                ->when(!empty($request->input('store')), function ($q) use ($request){
                    return $q->where('id_store', $request->input('store'));
                })
                ->when($request->has('area'), function ($q) use ($request){
                    return $q->where('id_area', $request->input('area'));
                })
                ->when($request->has('week'), function ($q) use ($request){
                    return $q->where('availability.week', $request->input('week'));
                })
                        ->select(
                            'stores.id',
                            'availability.date as avai_date',
                            'stores.name1',
                            'stores.name2',
                            'accounts.name as account_name',
                            'sub_areas.name as area_name',
                            'detail_availability.id as availability_id'
                            )->orderBy('avai_date')->get();

        foreach($datas as $data) {
                        $data['cek'] = 'NO';
            foreach ($categories as $category) {
                $data[$category->id] = $category->name;
                $data[$category->id.'sum'] = 0;
                $data[$category->id.'sumAvailable'] = 0;
                $products = Product::join('sub_categories','products.id_subcategory','sub_categories.id')
                                ->join('categories','sub_categories.id_category', 'categories.id')
                                ->where('categories.id',$category->id)
                                ->select('products.*')->get();
                foreach ($products as $product) {
                    $data[$category->id.'_'.$product->id] = $product->name;
                    $data[$category->id.'_'.$product->id] = '-';
                    $detail_data = DetailAvailability::where('detail_availability.id', $data->availability_id)
                                                    ->where('detail_availability.id_product',$product->id)
                                                    ->first();
                    if ($detail_data) {
                        $data[$category->id.'_'.$product->id] = $detail_data->available;
                        $data[$category->id.'sumAvailable'] += $detail_data->available;
                        $data[$category->id.'sum'] += 1;
                        $data['cek'] = 'CEK';
                    }

                }
                    if ($data[$category->id.'sum'] > 0){
                        $data[$category->id.'availability'] = round($data[$category->id.'sumAvailable'] / $data[$category->id.'sum'] * 100, 2).'%';

                    }else{
                        $data[$category->id.'availability'] = 'mobile';
                    }
            }

        } 

        // return response()->json($datas);

        return Datatables::of($datas)->make(true);
        // return response()->json($data);
    }


    public function availabilityIndex(){
        $data['categories'] = Category::get();
        return view('report.availabilityAch', $data);
    }

    public function availabilityAreaData(Request $request){
        
        if (!empty($request->input('periode'))) {
            $date = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $year   = Carbon::now()->format('Y');
            $month  = Carbon::now()->format('m');
        }

        if (!empty($request->input('week'))) {
            $week   = $request->input('week');
        }else{
            $week   = '(1,2,3,4)';
        }

        $categories = Category::get();
        $areas = Area::get();

        $data = new Collection();
        foreach ($areas as $area) {
                $item['id'] = $area->id;
                $item['area'] = $area->name;
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
                    AND year(`date`) = ".$year." and month(`date`) = ".$month."
                    AND a.week IN ".$week."
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
                    AND year(`date`) = ".$year." and month(`date`) = ".$month."
                    AND a.week IN ".$week."
                    AND dv.available = 1
                    ")[0]->data_count * 1;
                // return response()->json(round($totalProductAvailability / $totalProduct, 2) * 100);
                if ($totalProductAvailability == 0) {
                    $total = 0;
                }else{
                    $total = round($totalProductAvailability / $totalProduct, 2) * 100; 
                }
                $item['item_'.$category->name] = $total;
                $x++;
            }
                $data->push($item);
        }
        return Datatables::of($data)->make(true);
        // return response()->json($data);
    }

    public function availabilityAccountData(Request $request){
        
        if (!empty($request->input('periode'))) {
            $date = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $year   = Carbon::now()->format('Y');
            $month  = Carbon::now()->format('m');
        }

        if (!empty($request->input('week'))) {
            $week   = $request->input('week');
        }else{
            $week   = '(1,2,3,4)';
        }

        $categories = Category::get();
        $accounts = Account::get();

        $data = new Collection();
        foreach ($accounts as $account) {
                $item['id'] = $account->id;
                $item['area'] = $account->name;
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
                    AND year(`date`) = ".$year." and month(`date`) = ".$month."
                    AND a.week IN ".$week."
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
                    AND year(`date`) = ".$year." and month(`date`) = ".$month."
                    AND a.week IN ".$week."
                    AND dv.available = 1
                    ")[0]->data_count * 1;
                // return response()->json(round($totalProductAvailability / $totalProduct, 2) * 100);
                if ($totalProductAvailability == 0) {
                    $total = 0;
                }else{
                    $total = round($totalProductAvailability / $totalProduct, 2) * 100; 
                }
                $item['item_'.$category->name] = $total;
                $x++;
            }
                $data->push($item);
        }
        return Datatables::of($data)->make(true);
        // return response()->json($data);
    }

    public function availabilityExportXLS(Request $request)
    {
        $limitArea    = ($request->get('limitArea') == "null" ? null : $request->get('limitArea'));
        $limitAccount = ($request->get('limitAccount') == "null" ? null : $request->get('limitAccount'));

        $result = DB::transaction(function() use ($limitArea, $limitAccount){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Availability (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCAvailabilityJob($JobTrace, $limitArea, $limitAccount, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    // *********** DISPLAY SHARE ****************** //

    public function displayShareIndex(){

        $data['categories'] = Category::get();
        $data['brands'] = Brand::get();
        $data['jml_brand'] = Brand::get()->count();
        // return response()->json($data);

        return view('report.display-share-raw', $data);
    }

    public function displayShareSpgData(Request $request)
    {
        $categories = Category::get();
        $brands = Brand::get();

        $datas = DisplayShare::where('display_shares.deleted_at', null)
                ->join("stores", "display_shares.id_store", "=", "stores.id")
                ->join('sub_areas', 'stores.id_subarea', 'sub_areas.id')
                ->join('areas', 'sub_areas.id_area', 'areas.id')
                ->join('regions', 'areas.id_region', 'regions.id')
                ->join('accounts', 'stores.id_account', 'accounts.id')
                ->leftjoin('employee_sub_areas', 'stores.id', 'employee_sub_areas.id_subarea')
                ->leftjoin('employees as empl_tl', 'employee_sub_areas.id_employee', 'empl_tl.id')
                ->join("employees", "display_shares.id_employee", "=", "employees.id")
                ->leftjoin("detail_display_shares", "display_shares.id", "=", "detail_display_shares.id_display_share")
                ->groupby('display_shares.id_store')
                ->when($request->has('employee'), function ($q) use ($request){
                    return $q->where('display_shares.id_employee',$request->input('employee'));
                })
                ->when($request->has('periode'), function ($q) use ($request){
                    return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
                    ->whereYear('date', substr($request->input('periode'), 3));
                })
                ->when(!empty($request->input('store')), function ($q) use ($request){
                    return $q->where('id_store', $request->input('store'));
                })
                ->when($request->has('area'), function ($q) use ($request){
                    return $q->where('id_area', $request->input('area'));
                })
                ->select(
                    'display_shares.*',
                    'stores.name1 as store_name',
                    'employees.name as emp_name',
                    'regions.name as region_name',
                    'areas.name as area_name',
                    'empl_tl.name as tl_name',
                    'employees.status as jabatan',
                    'accounts.name as account_name'
                    )
                ->get();
            
        foreach($datas as $data)
        {
            foreach ($categories as $category) {
                    $data[$category->id.'_total_tier'] = 0;
                    $data[$category->id.'_total_depth'] = 0;
                foreach ($brands as $brand) {
                    $data[$category->id.'_'.$brand->id.'_tier'] = '-';
                    $data[$category->id.'_'.$brand->id.'_depth'] = '-';
                    $detail_data = DetailDisplayShare::where('detail_display_shares.id_display_share', $data->id)
                                                    ->where('detail_display_shares.id_category',$category->id)
                                                    ->where('detail_display_shares.id_brand',$brand->id)
                                                    ->first();
                    if ($detail_data) {
                        $data[$category->id.'_'.$brand->id.'_tier'] = $detail_data->tier;
                        $data[$category->id.'_'.$brand->id.'_depth'] = $detail_data->depth;

                        $data[$category->id.'_total_tier'] += $detail_data->tier;
                        $data[$category->id.'_total_depth'] += $detail_data->depth;

                    }
                }
            }

        }    

        return Datatables::of($datas)->make(true);
        // return response()->json($datas);
    }

    public function displayShareSpgDataExportXLS(Request $request)
    {
        $periode     = ($request->periode == "null" || empty($request->periode) ? Carbon::now()->format('m/Y') : $request->periode);
        $id_employee = ($request->id_employee == "null" || empty($request->id_employee) ? null : $request->id_employee);
        $id_store    = ($request->id_store == "null" || empty($request->id_store) ? null : $request->id_store);
        $id_area     = ($request->id_area == "null" || empty($request->id_area) ? null : $request->id_area);
        $limit       = ($request->limit == "null" || empty($request->limit) ? null : $request->limit);

        $result = DB::transaction(function() use ($periode, $id_employee, $id_store, $id_area, $limit){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Display Share - " . (is_null($id_employee) ? "All Employee" : Employee::where("id", $id_employee)->first()->name) . " - " . Carbon::parse("01/".$periode)->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCDisplayShareJob($JobTrace, $periode, $id_employee, $id_store, $id_area, $limit, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);

    }


    public function displayShareAch(){
        return view('report.display-share-ach');
    }

    public function displayShareReportAreaData(Request $request){
        
        if (!empty($request->input('periode'))) {
            $date   = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $date   = Carbon::now();
            $year   = $date->format('Y');
            $month  = $date->format('m');
        }

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
                                ->whereMonth('display_shares.date', $month)
                                ->whereYear('display_shares.date', $year)
                                ->groupby('display_shares.id_store')
                                ->pluck('display_shares.id');
            $categoryTB = 1;
            $categoryPF = 2;
            $persenTB = 40;
            $persenPF = 40;
            $data['hitTargetTB'] = 0;
            $data['hitTargetPF'] = 0;
            // $data['achTB'] = 0;
            // $data['achPF'] = 0;


            foreach ($dataActuals as $dataActual) {
                $actualDS = DetailDisplayShare::where('detail_display_shares.id_display_share',$dataActual);

                if ($actualDS) {
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
            }

            if ($data['store_panel_cover'] == 0) {
                    $data['achTB'] = 0;
            }else{
                $data['achTB'] = round($data['hitTargetTB'] / $data['store_panel_cover'] * 100, 2).'%';
            
            }if ($data['store_panel_cover'] == 0) {
                    $data['achPF'] = 0;
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

    public function displayShareReportSpgData(Request $request){
        
        if (!empty($request->input('periode'))) {
            $date   = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $date   = Carbon::now();
            $year   = $date->format('Y');
            $month  = $date->format('m');
        }

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
                                ->whereMonth('display_shares.date', $month)
                                ->whereYear('display_shares.date', $year)
                                ->groupby('display_shares.id_store')
                                ->pluck('display_shares.id');
            $categoryTB = 1;
            $categoryPF = 2;
            $persenTB = 40;
            $persenPF = 40;
            $data['hitTargetTB'] = 0;
            $data['hitTargetPF'] = 0;
            // $data['achTB'] = 0;
            // $data['achPF'] = 0;


            foreach ($dataActuals as $dataActual) {
                $actualDS = DetailDisplayShare::where('detail_display_shares.id_display_share',$dataActual);

                if ($actualDS) {
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
                    $data['tierPF'] = $actualPF->tier?? '';
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
            }

            if ($data['store_panel_cover'] == 0) {
                    $data['achTB'] = 0;
            }else{
                $data['achTB'] = round($data['hitTargetTB'] / $data['store_panel_cover'] * 100, 2).'%';
            
            }if ($data['store_panel_cover'] == 0) {
                    $data['achPF'] = 0;
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

    public function displayShareReportMdData(Request $request){
        
        if (!empty($request->input('periode'))) {
            $date   = explode('/', $request->input('periode'));
            $year   = $date[1];
            $month  = $date[0];
        }else{
            $date   = Carbon::now();
            $year   = $date->format('Y');
            $month  = $date->format('m');
        }

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
                                ->whereMonth('display_shares.date', $month)
                                ->whereYear('display_shares.date', $year)
                                ->groupby('display_shares.id_store')
                                ->pluck('display_shares.id');
            $categoryTB = 1;
            $categoryPF = 2;
            $persenTB = 40;
            $persenPF = 40;
            $data['hitTargetTB'] = 0;
            $data['hitTargetPF'] = 0;
            // $data['achTB'] = 0;
            // $data['achPF'] = 0;

            foreach ($dataActuals as $dataActual) {
                $actualDS = DetailDisplayShare::where('detail_display_shares.id_display_share',$dataActual);

                if ($actualDS) {
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
                    $data['tierPF'] = $actualPF->tier?? '';
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
            }

            if ($data['store_panel_cover'] == 0) {
                    $data['achTB'] = 0;
            }else{
                $data['achTB'] = round($data['hitTargetTB'] / $data['store_panel_cover'] * 100, 2).'%';
            
            }if ($data['store_panel_cover'] == 0) {
                    $data['achPF'] = 0;
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

    public function displayShareReportExportXLS(Request $request)
    {
        $limitArea = ($request->limitArea == "null" || empty($request->limitArea) ? null : $request->limitArea);
        $limitSPG = ($request->limitSPG == "null" || empty($request->limitSPG) ? null : $request->limitSPG);
        $limitMD = ($request->limitMD == "null" || empty($request->limitMD) ? null : $request->limitMD);

        $result = DB::transaction(function() use ($limitArea, $limitSPG, $limitMD){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Display Share Achievement - " . Carbon::now()->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCDisplayShareAchievementJob($JobTrace, $limitArea, $limitSPG, $limitMD, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    // *********** ADDITIONAL DISPLAY ****************** //


    public function additionalDisplayIndex(){
        return view('report.additional-display');
    }

    public function additionalDisplaySpgData(Request $request)
    {

        $datas = AdditionalDisplay::
        where('additional_displays.deleted_at', null)
                ->join("stores", "additional_displays.id_store", "=", "stores.id")
                ->join('sub_areas', 'stores.id_subarea', 'sub_areas.id')
                ->join('areas', 'sub_areas.id_area', 'areas.id')
                ->join('regions', 'areas.id_region', 'regions.id')
                ->leftjoin('employee_sub_areas', 'stores.id', 'employee_sub_areas.id_subarea')
                ->leftjoin('employees as empl_tl', 'employee_sub_areas.id_employee', 'empl_tl.id')
                ->join("employees", "additional_displays.id_employee", "=", "employees.id")
                ->leftjoin("detail_additional_displays", "additional_displays.id", "=", "detail_additional_displays.id_additional_display")
                ->join("jenis_displays", "detail_additional_displays.id_jenis_display", "=", "jenis_displays.id")
                ->when($request->has('employee'), function ($q) use ($request){
                    return $q->where('additional_displays.id_employee',$request->input('employee'));
                })
                ->when($request->has('periode'), function ($q) use ($request){
                    return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
                    ->whereYear('date', substr($request->input('periode'), 3));
                })
                ->when(!empty($request->input('store')), function ($q) use ($request){
                    return $q->where('id_store', $request->input('store'));
                })
                ->when($request->has('area'), function ($q) use ($request){
                    return $q->where('id_area', $request->input('area'));
                })
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
                // return $datas;
            
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
        // return response()->json($datas);

        return Datatables::of($datas)->make(true);
        return response()->json($datas);

    }

    public function additionalDisplayIndexExportXLS(Request $request)
    {
        $data['id_employee'] = ($request->id_employee == "null" || empty($request->id_employee) ? null : $request->id_employee);
        $data['id_store'] = ($request->id_store == "null" || empty($request->id_store) ? null : $request->id_store);
        $data['id_area'] = ($request->id_area == "null" || empty($request->id_area) ? null : $request->id_area);
        $data['periode'] = ($request->periode == "null" || empty($request->periode) ? null : $request->periode);
        $data['limit'] = ($request->limit == "null" || empty($request->limit) ? null : $request->limit);

        $result = DB::transaction(function() use ($data){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Additional Display - " . Carbon::now()->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCAdditionalDisplayJob($JobTrace, $data, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function additionalDisplayAch(){
        return view('report.additional-display-ach');
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
                    $data['ach'] = 0;
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
                    $data['ach'] = 0;
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
                    $data['ach'] = 0;
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

    public function additionalDisplayExportXLS(Request $request)
    {
        $limitArea = ($request->limitArea == "null" || empty($request->limitArea) ? null : $request->limitArea);
        $limitSPG = ($request->limitSPG == "null" || empty($request->limitSPG) ? null : $request->limitSPG);
        $limitMD = ($request->limitMD == "null" || empty($request->limitMD) ? null : $request->limitMD);

        $result = DB::transaction(function() use ($limitArea, $limitSPG, $limitMD){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "MTC - Report Additional Display Achievement - " . Carbon::now()->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportMTCAdditionalDisplayAchievementJob($JobTrace, $limitArea, $limitSPG, $limitMD, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e;
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }



    // ************ SMD PASAR ************ //
    public function SMDpasar(Request $request)
    {
        $employeePasar = EmployeePasar::with([
            'employee','pasar','pasar.subarea.area',
            'employee.position'
        ])->select('employee_pasars.*');
        $report = array();
        $id = 1;
        $periode = $request->input('periode');
        if (Carbon::now()->month == substr($periode, 0, 2)) {
            $date = Carbon::now();
            $day = $date->day;
        } else {
            $date = Carbon::parse(substr($periode, 3)."-".substr($request->input('periode'), 0, 2)."-01");
            $day = $date->endOfMonth()->day;
        }
        for ($i=1; $i <= $day; $i++) {
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
                        'stockist' => $this->getStockist($data, $periode, $i),
                        'bulan' => $date->month,
                        'tanggal' => $i,
                        'call' => $this->getCall($data, $periode, $i),
                        'ro' => $this->getRo($data, $periode, $i),
                        'cbd' => $this->getCbd($data, $periode, $i),
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

    public function exportSMDsummary()
    {
        $employeePasar = EmployeePasar::with([
            'employee','pasar','pasar.subarea.area',
            'employee.position'
        ])->select('employee_pasars.*');
        if ($employeePasar->count() > 0) {
            $report = array();
            for ($i=1; $i <= Carbon::now()->day ; $i++) {
                foreach ($employeePasar->get() as $data) {
                    if ($data->employee->position->level == 'mdgtc') {
                        $report[] = array(
                            'area' => $data->pasar->subarea->area->name,
                            'nama' => $data->employee->name,
                            'jabatan' => $data->employee->position->name,
                            'pasar' =>   $data->pasar->name,
                            'stockist' => $this->getStockist($data, $i),
                            'bulan' => Carbon::now()->month,
                            'tanggal' => $i,
                            'call' => ($this->getCall($data, $i) ?: "-"),
                            'ro' => ($this->getRo($data, $i) ?: "-"),
                            'cbd' => ($this->getCbd($data, $i) ?: "-"),
                        );
                    }
                }
            }
            $filename = "AttandanceSPGReport".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($report) {
                $excel->sheet('AttandanceSPGReport', function($sheet) use ($report)
                {
                    $sheet->fromArray($report);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal Unduh!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function SMDattendance(Request $request)
    {
        $employee = AttendanceOutlet::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $employee->whereHas('attendance.employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
         if ($request->has('periode')) {
            $employee->whereMonth('checkin', substr($request->input('periode'), 0, 2));
            $employee->whereYear('checkin', substr($request->input('periode'), 3));
        }  
        if ($request->has('pasar')) {
            $employee->whereHas('outlet.employeePasar.pasar', function($q) use ($request){
                return $q->where('id_pasar', $request->input('pasar'));
            });
        } 
         if ($request->has('area')) {
            $employee->whereHas('outlet.employeePasar.pasar.subarea.area', function($q) use ($request){
                return $q->where('id_area', $request->input('area'));
            });
        }
        $data = array();
        $absen = array();
        $id = 1;
        foreach ($employee->get() as $val) {
            if ($val->attendance->employee->position->level == 'mdgtc')
            {
                $checkin = Carbon::parse($val->checkin)->setTimezone($val->attendance->employee->timezone->timezone)->format('H:i:s');
                $checkout = ($val->checkout ? Carbon::parse($val->checkout)->setTimezone($val->attendance->employee->timezone->timezone)->format('H:i:s') : "Belum Check-out");
                $data[] = array(
                    'id' => $id++,
                    'region' => (isset($val->outlet->employeePasar->pasar->name) ? $val->outlet->employeePasar->pasar->name : ""),
                    'area' => (isset($val->outlet->employeePasar->pasar->subarea->area->name) ? $val->outlet->employeePasar->pasar->subarea->area->name : ""),
                    'subarea' => (isset($val->outlet->employeePasar->pasar->subarea->name) ? $val->outlet->employeePasar->pasar->subarea->name : ""),
                    'nama' => (isset($val->attendance->employee->name) ? $val->attendance->employee->name : ""),
                    'jabatan' => (isset($val->attendance->employee->position->name) ? $val->attendance->employee->position->name : ""),
                    'pasar' => (isset($val->outlet->employeePasar->pasar->name) ? $val->outlet->employeePasar->pasar->name : ""),
                    'outlet' => (isset($val->outlet->name) ? $val->outlet->name : ""),
                    'tanggal' => Carbon::parse($val->checkin)->day,
                    'checkin' => $checkin." ".$val->attendance->employee->timezone->name,
                    'checkout' => $checkout." ".$val->attendance->employee->timezone->name
                );
            }
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

    // ************ MOTORIK ************ //
    public function Motorikattendance(Request $request)
    {
        $employee = AttendanceBlock::whereMonth('checkin', substr($request->input('periode'), 0, 2))
        ->whereYear('checkin', substr($request->input('periode'), 3))->get();
        $id = 1;
        $data = array();
        foreach ($employee as $val) {
            if ($val->attendance->employee->position->level == 'motoric') {
                $data[] = array(
                    'id'        => $id++,
                    'region'    => (isset($val->block->subArea->area->region->name) ? $val->block->subArea->area->region->name : ""),
                    'area'      => (isset($val->block->subArea->area->name) ? $val->block->subArea->area->name : ""),
                    'subarea'   => (isset($val->block->subArea->name) ? $val->block->subArea->name : ""),
                    'block'     => (isset($val->block->name) ? $val->block->name : ""),
                    'nama'      => (isset($val->attendance->employee->name) ? $val->attendance->employee->name : ""),
                    'jabatan'   => (isset($val->attendance->employee->position->name) ? $val->attendance->employee->position->name : ""),
                    'tanggal'   => Carbon::parse($val->checkin)->day,
                    'checkin'   => Carbon::parse($val->checkin)->format('H:m:s'),
                    'checkout'  => ($val->checkout ? Carbon::parse($val->checkout)->format('H:m:s') : "Belum Check-out")
                );
            }
        }
        return Datatables::of(collect($data))->make(true);
    }

    public function exportMptorikAttandance(Request $request)
    {
        $employee = AttendanceBlock::whereMonth('checkin', substr($request->input('periode'), 0, 2))
        ->whereYear('checkin', substr($request->input('periode'), 3));
        if ($employee->count() > 0) {
		    foreach ($employee->get() as $val) {
                if ($val->attendance->employee->position->level == 'motoric') {
		    	    $data[] = array(
                    'Region'    => (isset($val->block->subArea->area->region->name) ? $val->block->subArea->area->region->name : ""),
                    'Area'      => (isset($val->block->subArea->area->name) ? $val->block->subArea->area->name : ""),
                    'Subarea'   => (isset($val->block->subArea->name) ? $val->block->subArea->name : ""),
                    'Block'     => (isset($val->block->name) ? $val->block->name : ""),
                    'Nama'      => (isset($val->attendance->employee->name) ? $val->attendance->employee->name : ""),
                    'Jabatan'   => (isset($val->attendance->employee->position->name) ? $val->attendance->employee->position->name : ""),
                    'Tanggal'   => Carbon::parse($val->checkin)->day,
                    'Check-in'  => Carbon::parse($val->checkin)->format('H:m:s'),
                    'Check-out' => ($val->checkout ? Carbon::parse($val->checkout)->format('H:m:s') : "Belum Check-out")
                    );
                }
            }
        
		    $filename = "AttandanceMotorikReport".Carbon::now().".xlsx";
		    return Excel::create($filename, function($excel) use ($data) {
		    	$excel->sheet('AttandanceMotorikReport', function($sheet) use ($data)
		    	{
		    		$sheet->fromArray($data);
		    	});
            })->download();
        } else {
            return redirect()->back()
            ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal Unduh!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function motorikDistPF(Request $request)
    {
        $dist = DistributionMotoric::whereMonth('date', substr($request->input('periode'), 0, 2))
        ->whereYear('date', substr($request->input('periode'), 3))->get();
        $data = array();
        $product = array();
        $id = 1;
        foreach ($dist as $key => $value) {
            if ($value->employee->position->level == 'motoric') {
                $data[] = array(
                    'id'        => $id++,
                    'nama'      => $value->employee->name,
                    'block'     => (isset($value->block->name) ? $value->block->name : "-"),
                    'tanggal'   => Carbon::parse($value->date)->day,
                
                );
            }
        }
        $dt = Datatables::of(collect($data));
        $columns = array();
        foreach (Product::get() as $pdct) {
            $columns[] = 'product-'.$pdct->id;
            $dt->addColumn('product-'.$pdct->id, function($dist) use ($pdct) {
                $distribution = DistributionMotoricDetail::where([
                    'id_distribution' => $dist['id'],
                    'id_product' => $pdct->id
                ])->first();
                return $distribution['qty_actual']."&nbsp;".$distribution['satuan'];
            });
        }
        $dt->rawColumns($columns);
        return $dt->make(true);
    }

    public function exportMotorikDistPF(Request $request)
    {
        $dist = DistributionMotoric::whereMonth('date', substr($request->input('periode'), 0, 2))
        ->whereYear('date', substr($request->input('periode'), 3));
        if ($dist->count() > 0) {
            foreach ($dist->get() as $key => $value) {
                if ($value->employee->position->level == 'motoric') {
                    $detail = DistributionMotoricDetail::where('id_distribution',$value->id)->get();
                    $data[] = array(
                    'Nama Motorik'  => $value->employee->name,
                    'Block'         => (isset($value->block->name) ? $value->block->name : "-"),
                    'Tanggal'       => Carbon::parse($value->date)->day
                    );
                }
            }
            $getId = array_column(\App\DistributionMotoricDetail::get(['id_product'])->toArray(),'id_product');
            $productList = \App\Product::whereIn('id', $getId)->get();
            foreach ($productList as $pro) {
                $data[$key][$pro->name] = "-";
            }
            foreach ($detail as $det) {
                $data[$key][$det->product->name] = $det->qty_actual." ".$det->satuan;
            }
        
		    $filename = "ReportMotorikDistPF".Carbon::now().".xlsx";
		    return Excel::create($filename, function($excel) use ($data) {
		    	$excel->sheet('ReportMotorikDistPF', function($sheet) use ($data)
		    	{
		    		$sheet->fromArray($data);
		    	});
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function MotorikSales(Request $request)
    {
        $sales = SalesMotoric::whereMonth('date', substr($request->input('periode'), 0, 2))
        ->whereYear('date', substr($request->input('periode'), 3))->get();
        $data = array();
        $id = 1;
        foreach ($sales as $value) {
            if ($value->employee->position->level == 'motoric') {
                $data[] = array(
                    'id'        => $id++,
                    'id_sales'  => $value->id,
                    'nama'      => (isset($value->employee->name) ? $value->employee->name : ""),
                    'block'     => (isset($value->block->name) ? $value->block->name : ""),
                    'date'   => (isset($value->date) ? $value->date : ""),
                );
            }
        }
        $getId = array_column(\App\SalesMotoricDetail::get(['id_product'])->toArray(),'id_product');
        $product = \App\Product::whereIn('id', $getId)->get();
        $dt = Datatables::of(collect($data));
        $columns = array();
        foreach ($product as $pdct) {
            $columns[] = 'product-'.$pdct->id;
            $dt->addColumn('product-'.$pdct->id, function($sales) use ($pdct) {
                $sale = \App\SalesMotoricDetail::where([
                    'id_sales' => $sales['id_sales'],
                    'id_product' => $pdct->id
                ])->first();
                return $sale['qty_actual']."&nbsp;".$sale['satuan'];
            });
        }
        $dt->rawColumns($columns);
        return $dt->make(true);
    }

    public function exportMotorikSales(Request $request)
    {
        $sales = SalesMotoric::whereMonth('date', substr($request->input('periode'), 0, 2))
        ->whereYear('date', substr($request->input('periode'), 3));
        if ($sales->count() > 0) {
            $product = array();
            foreach ($sales->get() as $key => $value) {
                if ($value->employee->position->level == 'motoric') {
                    $detail = SalesMotoricDetail::where('id_sales',$value->id)->get();
                    $data[] = array(
                        'Nama Motorik'  => (isset($value->employee->name) ? $value->employee->name : ""),
                        'Block'         => (isset($value->block->name) ? $value->block->name : ""),
                        'Date'          => (isset($value->date) ? $value->date : ""),
                    );
                    $getId = array_column(\App\SalesMotoricDetail::get(['id_product'])->toArray(),'id_product');
                    $productList = \App\Product::whereIn('id', $getId)->get();
                    foreach ($productList as $pro) {
                        $data[$key][$pro->name] = "-";
                    }
                    foreach ($detail as $det) {
                        $data[$key][$det->product->name] = $det->qty_actual." ".$det->satuan;
                    }
                }
            }
            $filename = "MotorikSales".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('MotorikSales', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

       // ************ DEMO COOKING ************ //
    public function kunjunganDc(Request $request)
    {
        $plan = PlanDc::with('planEmployee')->orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $plan->whereHas('planEmployee.employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
         if ($request->has('periode')) {
            $plan->whereMonth('date', substr($request->input('periode'), 0, 2));
            $plan->whereYear('date', substr($request->input('periode'), 3));
        } 
        return Datatables::of($plan->get())
        ->addColumn('action', function ($plan) {
            if (isset($plan->photo)) {
                $img_url = asset('/uploads/plan')."/".$plan->photo;
            } else {
                $img_url = asset('/').'no-image.jpg';
            }
            return "<img src='".$img_url."' width='50px'/>";
        
        })
        ->addColumn('planEmployee', function($plan) {
            $dist = PlanEmployee::where(['id_plandc'=>$plan->id])->get();
            $distList = array();
            foreach ($dist as $data) {
                $distList[] = $data->employee->name;
            }
            return rtrim(implode(',', $distList), ',');
        })->make(true);
    }

    public function exportKunjunganDc(Request $request)
    {
        $employee = ($request->employee == "null" || empty($request->employee) ? null : $request->employee);
        
        $plan = PlanDc::with('planEmployee')->orderBy('created_at', 'DESC')
        ->when($employee, function($q) use ($employee)
        {
            $q->whereHas('planEmployee.employee', function($q2) use ($employee){
                return $q2->where('id_employee', $employee);
            });
        })
        ->when($request->has('periode'), function($q) use ($request)
        {
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->get();
        // return response()->json($plan);
        if ($plan->count() > 0) {
            $product = array();
            foreach ($plan as $key => $value) {
                $dist = PlanEmployee::where(['id_plandc'=>$value->id])->pluck('id_employee');
        // return response()->json($dist);
                $emplist = implode(', ', Employee::whereIn('id', $dist)->pluck('name')->toArray());


                $data[] = array(
                        'Nama Demo Cooking'=> (isset($emplist) ? $emplist : ""),
                        'Date'             => (isset($value->date) ? $value->date : ""),
                        'Plan'             => (isset($value->plan) ? $value->plan : ""),
                        'Stockist'         => (isset($value->stocklist) ? $value->stocklist : ""),
                        'channel'          => (isset($value->channel) ? $value->channel : ""),
                    );
            }
            $filename = "DemoCookingPlan".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('DemoCooking', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }

    }

    public function DcSales(Request $request)
    {
        $sales = SalesDc::orderBy('created_at', 'DESC')
        ->when($request->has('employee'), function($q) use ($request)
        {
            return $q->whereHas('employee', function($q2) use ($request){
                return $q2->where('id_employee', $request->input('employee'));
            });
        })
        ->when($request->has('periode'), function($q) use ($request)
        {
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->get();
         
        $data = array();
        $id = 1;
        foreach ($sales as $value) {
            if ($value->employee->position->level == 'dc') {
                $data[] = array(
                    'id'        => $id++,
                    'id_sales'  => $value->id,
                    'nama'      => (isset($value->employee->name) ? $value->employee->name : ""),
                    'place'     => (isset($value->place) ? $value->place : ""),
                    'icip_icip'         => $value->icip_icip ?? "",
                    'effective_contact' => $value->effective_contact ?? "",
                    'tanggal'   => (isset($value->date) ? $value->date : ""),
                );
            }
        }
        $getId = array_column(\App\SalesDcDetail::get(['id_product'])->toArray(),'id_product');
        $product = \App\Product::whereIn('id', $getId)->get();
        $dt = Datatables::of(collect($data));
        $columns = array();
        foreach ($product as $pdct) {
            $columns[] = 'product-'.$pdct->id;
            $dt->addColumn('product-'.$pdct->id, function($sales) use ($pdct) {
                $sale = \App\SalesDcDetail::where([
                    'id_sales' => $sales['id_sales'],
                    'id_product' => $pdct->id
                ])->first();
                return $sale['qty_actual']."&nbsp;".$sale['satuan'];
            });
        }
        $dt->rawColumns($columns);
        return $dt->make(true);
    }

    public function exportDcSales(Request $request)
    {
        $employee = ($request->employee == "null" || empty($request->employee) ? null : $request->employee);

        // return response()->json($request);
        $sales = SalesDc::orderBy('created_at', 'DESC')
        ->when($employee, function($q) use ($employee)
        {
            return $q->where('id_employee', $employee);
        })
        ->when($request->has('periode'), function($q) use ($request)
        {
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->get();

        if ($sales->count() > 0) {
            $product = array();
            foreach ($sales as $key => $value) {
                if ($value->employee->position->level == 'dc') {
                    $detail = SalesDcDetail::where('id_sales',$value->id)->get();
                    $data[] = array(
                        'Nama Demo Cooking' => (isset($value->employee->name) ? $value->employee->name : ""),
                        'Place'             => (isset($value->place) ? $value->place : ""),
                        'Date'              => (isset($value->date) ? $value->date : ""),
                        'Icip-icip'         => (isset($value->icip_icip) ? $value->icip_icip : ""),
                        'Effective Contact' => (isset($value->effective_contact) ? $value->effective_contact : ""),
                    );
                    $getId = array_column(\App\SalesDcDetail::get(['id_product'])->toArray(),'id_product');
                    $productList = \App\Product::whereIn('id', $getId)->get();
                    foreach ($productList as $pro) {
                        $data[$key][$pro->name] = "-";
                    }
                    foreach ($detail as $det) {
                        $data[$key][$det->product->name] = $det->qty_actual." ".$det->satuan;
                    }
                }
            }
            $filename = "DemoCookingSales".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('DemoCooking', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function DcSampling(Request $request)
    {
        $sales = SamplingDc::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $sales->whereHas('employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
         if ($request->has('periode')) {
            $sales->whereMonth('date', substr($request->input('periode'), 0, 2));
            $sales->whereYear('date', substr($request->input('periode'), 3));
        }
        $data = array();
        $id = 1;
        foreach ($sales->get() as $value) {
            if ($value->employee->position->level == 'dc'){
                $data[] = array(
                    'id'            => $id++,
                    'id_sales'      => $value->id,
                    'id_employee'   => $value->id_employee,
                    'nama'          => $value->employee->name,
                    'place'         => (isset($value->place) ? $value->place : ""),
                    'date'       => (isset($value->date) ? $value->date : "")
                );
            }
        }
        $dt = Datatables::of(collect($data));
        $columns = array();
        foreach (Product::get() as $pdct) {
            $columns[] = 'product-'.$pdct->id;
            $dt->addColumn('product-'.$pdct->id, function($sales) use ($pdct) {
                $sampling = SamplingDc::where([
                    'id_employee' => $sales['id_employee'],
                    'date'        => $sales['date'],
                ])->get(['id'])->toArray();

                $getId = array_column($sampling,'id');
                $detail = SamplingDcDetail::whereIn('id_sales', $getId)
                ->where('id_product', $pdct['id'])
                ->get();
                $satuan = array();
                foreach ($detail as $value) {
                     $satuan[] = $value->qty_actual."&nbsp;".$value->satuan;
                } 
                return implode(', ', $satuan);
            });
        }
        $dt->rawColumns($columns);
        return $dt->make(true);
    }

    public function exportDcSampling(Request $request)
    {
        $employee = ($request->employee == "null" || empty($request->employee) ? null : $request->employee);

        $sales = SamplingDc::orderBy('created_at', 'DESC')
        ->when($employee, function($q) use ($employee)
        {
            return $q->where('id_employee', $employee);
        })
        ->when($request->has('periode'), function($q) use ($request)
        {
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->get();
        if ($sales->count() > 0) {
            $product = array();
            foreach ($sales as $key => $value) {
                if ($value->employee->position->level == 'dc') {
                    $detail = SamplingDcDetail::where('id_sales',$value->id)->get();
                    $data[] = array(
                        'Nama Demo Cooking' => (isset($value->employee->name) ? $value->employee->name : ""),
                        'Place'             => (isset($value->place) ? $value->place : ""),
                        'Date'              => (isset($value->date) ? $value->date : ""),
                    );
                    $getId = array_column(\App\SalesDcDetail::get(['id_product'])->toArray(),'id_product');
                    $productList = \App\Product::whereIn('id', $getId)->get();
                    foreach ($productList as $pro) {
                        $data[$key][$pro->name] = "-";
                    }
                    foreach ($detail as $det) {
                        $data[$key][$det->product->name] = $det->qty_actual." ".$det->satuan;
                    }
                }
            }
            $filename = "DemoCookingSalesSampling".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('DemoCooking', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function documentationDC(Request $request)
    {
        $data = array();
        $employee = DocumentationDc::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $employee->whereHas('employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
         if ($request->has('periode')) {
            $employee->whereMonth('date', substr($request->input('periode'), 0, 2));
            $employee->whereYear('date', substr($request->input('periode'), 3));
        }
        if ($request->has('type')) {
            $employee->where('type', $request->input('type'));
        } 
        $id = 1;
        foreach ($employee->get() as $val) {
            if ($val->employee->position->level == 'dc') {
                $data[] = array(
                    'id'    => $id++,
                    'name'  =>  $val->employee->name,
                    'date'  => (isset($val->date) ? $val->date : ""),
                    'place' => (isset($val->place) ? $val->place : ""),
                    'type'  => (isset($val->type) ? $val->type : ""),
                    'note'  => (isset($val->note) ? $val->note : ""),
                    'photo1' => (isset($val->photo1) ? $val->photo1: ""),
                    'photo2' => (isset($val->photo2) ? $val->photo2: ""),
                    'photo3' => (isset($val->photo3) ? $val->photo3: ""),
                );
            }
        }
        return Datatables::of(collect($data))
        ->addColumn('action', function($employee) {
            if ($employee['photo1'] != "") {
                $img_url = asset('/uploads/documentation')."/".$employee['photo1'];
                $foto = "<img src='".$img_url."' width='50px'/>";
            } else {
                $img_url = "";
                $foto = "<img src='".$img_url."' width='50px'/>";
            }
            return $foto;
        })->make(true);
    }

    public function ExportdocumentationDC(Request $request)
    {
        $employee = ($request->employee == "null" || empty($request->employee) ? null : $request->employee);
        $type = ($request->type == "null" || empty($request->type) ? null : $request->type);

        $doc = DocumentationDc::orderBy('created_at', 'DESC')
        ->when($employee, function($q) use ($employee)
        {
            return $q->where('id_employee', $employee);
        })
        ->when($type, function($q) use ($type)
        {
            return $q->where('type', $type);
        })
        ->when($request->has('periode'), function($q) use ($request)
        {
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->get();

        if ($doc->count() > 0) {
            foreach ($doc as $val) {
                if ($val->employee->position->level == 'dc') {
                    $data[] = array(
                        'Nama DC'   =>  $val->employee->name,
                        'Date'      => (isset($val->date) ? $val->date : ""),
                        'Palce'     => (isset($val->place) ? $val->place : ""),
                        'Type'      => (isset($val->type) ? $val->type : ""),
                        'Note'      => (isset($val->note) ? $val->note : "")
                    );
                }
            }
            $filename = "DemoCookingDocumentation".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('DemoCookingDocumentation', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function inventoriDC($id_employee)
    {
        $data = ReportInventori::where("id_employee", $id_employee)->get();
        return Datatables::of(collect($data))
        ->addColumn("employee", function($item){
            return Employee::where("id", $item->id_employee)->first()->name;
        })
        ->addColumn("item", function($item){
            return PropertiDc::where("id", $item->id_properti_dc)->first()->item;
        })
        ->addColumn("dokumentasi", function($item){
            return (isset($item->photo) ? "<img src='".asset($item->photo)."' style='min-width: 149px;max-width: 150px;'>" : "-");
        })
        ->rawColumns(["dokumentasi"])
        ->make(true);
    }

    public function inventoriDCAdd(Request $req)
    {
        $PropertiDcs = PropertiDc::get();
        $result = DB::transaction(function() use ($PropertiDcs, $req){
            foreach ($PropertiDcs as $PropertiDc)
            {
                $countReportInventori = ReportInventori::where("id_employee", $req->id_employee)->where("id_properti_dc", $PropertiDc->id)->count();
                if ($countReportInventori == 0)
                {
                    ReportInventori::create([
                        "no_polisi"       => strtoupper($req->no_polisi),
                        "id_employee"     => $req->id_employee,
                        "id_properti_dc"  => $PropertiDc->id,
                        "quantity"        => 0,
                        "actual"          => 0
                    ]);
                }
            }
        });

        return redirect(route('report.demo.inventori'))->with([
                'type'   => 'success',
                'title'  => 'Berhasil<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Data berhasil diperbarui!'
        ]);
    }

    // use \App\Traits\ExportDCReportInventoriTrait;

    public function inventoriDCExportXLS()
    {
        // return $this->DCReportInventoriExportTrait("cc");
        $result = DB::transaction(function(){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "Demo Cooking - Report Inventori (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportDCReportInventoriJob($JobTrace, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function SMDdistpf(Request $request)
    {
        
        $dist = Distribution::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $dist->whereHas('employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
         if ($request->has('periode')) {
            $dist->whereMonth('date', substr($request->input('periode'), 0, 2));
            $dist->whereYear('date', substr($request->input('periode'), 3));
        }  
        if ($request->has('pasar')) {
            $dist->whereHas('outlet.employeePasar.pasar', function($q) use ($request){
                return $q->where('id_pasar', $request->input('pasar'));
            });
        } 
        $data = array();
        $product = array();
        $id = 1;
        foreach ($dist->get() as $key => $value) {
            if ($value->employee->position->level == 'mdgtc') {
            $data[] = array(
                'id'            => $id++,
                'id_outlet'     => $value->id_outlet,
                'id_employee'   => $value->id_employee,
                'date'          => $value->date,
                'nama'          => $value->employee->name,
                'pasar'         => (isset($value->outlet->employeePasar->pasar->name) ? $value->outlet->employeePasar->pasar->name : ""),
                'tanggal'       => Carbon::parse($value->date)->day,
                'outlet'        => (isset($value->outlet->name) ? $value->outlet->name : "") 
            );
        } 
        }
        $dt = Datatables::of(collect($data));
        $columns = array();
        foreach (Product::get() as $pdct) {
            $columns[] = 'product-'.$pdct->id;
            $dt->addColumn('product-'.$pdct->id, function($dist) use ($pdct) {
                $distribution = Distribution::where([
                    'id_outlet' => $dist['id_outlet'],
                    'id_employee' => $dist['id_employee'],
                    'date' => $dist['date'],
                ])->get(['id'])->toArray();

                $getId = array_column($distribution,'id');
                $detail = DistributionDetail::whereIn('id_distribution', $getId)
                ->where('id_product', $pdct['id'])
                ->get();
                $satuan = array();
                foreach ($detail as $value) {
                     $satuan[] = $value->qty_actual."&nbsp;".$value->satuan;
                } 
                return implode(', ', $satuan);
            });
        }
        $dt->rawColumns($columns);
        return $dt->make(true);
    }

    public function SMDsales(Request $request)
    {
        $sales = SalesMd::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $sales->whereHas('employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
         if ($request->has('periode')) {
            $sales->whereMonth('date', substr($request->input('periode'), 0, 2));
            $sales->whereYear('date', substr($request->input('periode'), 3));
        }  
        if ($request->has('pasar')) {
            $sales->whereHas('outlet.employeePasar.pasar', function($q) use ($request){
                return $q->where('id_pasar', $request->input('pasar'));
            });
        } 
        $data = array();
        $id = 1;
        foreach ($sales->get() as $value) {
            if($value->employee->position->level == 'mdgtc'){
                $data[] = array(
                    'id'            => $id++,
                    'id_outlet'     => $value->id_outlet,
                    'id_employee'   => $value->id_employee,
                    'date'          => (isset($value->date) ? $value->date : ""),
                    'nama'          => (isset($value->employee->name) ? $value->employee->name : ""),
                    'pasar'         => (isset($value->outlet->employeePasar->pasar->name) ? $value->outlet->employeePasar->pasar->name : ""),
                    'tanggal'       => $value->date,
                    'outlet'        => (isset($value->outlet->name) ? $value->outlet->name : "")
                );
            }
        }
        $getId = array_column(\App\SalesMdDetail::get(['id_product'])->toArray(),'id_product');
        $product = \App\Product::whereIn('id', $getId)->get();
        $dt = Datatables::of(collect($data));
        $columns = array();
        foreach ($product as $pdct) {
            $columns[] = 'product-'.$pdct->id;
            $dt->addColumn('product-'.$pdct->id, function($sales) use ($pdct) {
                $sale = \App\SalesMd::where([
                    'id_employee' => $sales['id_employee'],
                    'id_outlet' => $sales['id_outlet'],
                    'date' => $sales['date'],
                ])->get(['id'])->toArray();
                $getIdSale = array_column($sale,'id');
                $detail = \App\SalesMdDetail::whereIn('id_sales', $getIdSale)
                ->where('id_product', $pdct['id'])
                ->get();
                $satuan = array();
                foreach ($detail as $value) {
                     $satuan[] = $value->qty_actual."&nbsp;".$value->satuan;
                } 
                // $html = "<table class='table table-bordered'>";
                // $html .= "<tr>";
                // $html .= "<td class='bg-gd-primary text-white'>Quantity</td>";
                // $html .= "<td>".$sale['qty']."</td>";
                // $html .= "<td class='bg-gd-primary text-white'>Actual</td>";
                // $html .= "<td>".$sale['qty_actual']."</td>";
                // $html .= "<td class='bg-gd-primary text-white'>Satuan</td>";
                // $html .= "<td>".$sale['satuan']."</td>";
                // $html .= "</tr>";
                // $html .= "</table>";
                return implode(", ", $satuan);
            });
        }
        $dt->rawColumns($columns);
        return $dt->make(true);
    }


    public function exportMdPasar(Request $request)
    {
        $employee = ($request->employee == "null" || empty($request->employee) ? null : $request->employee);
        $pasar = ($request->pasar == "null" || empty($request->pasar) ? null : $request->pasar);

        $sales = SalesMD::orderBy('created_at', 'DESC')
        ->when($employee, function($q) use ($employee)
        {
            return $q->where('id_employee', $employee);
        })
        ->when($pasar, function($q) use ($pasar)
        {
            $sales->whereHas('outlet.employeePasar.pasar', function($q) use ($pasar){
                return $q->where('id_pasar', $pasar);
            });
        })
        ->when($request->has('periode'), function($q) use ($request)
        {
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->get();

        if ($sales->count() > 0) {
            foreach ($sales as $key => $val) {
                if ($val->employee->position->level == 'mdgtc'){
                    $detail = SalesMdDetail::where('id_sales',$val->id)->get();
                    $data[] = array(
                        'Employee'  => (isset($val->employee->name) ? $val->employee->name : ""),
                        'Pasar'     => (isset($val->outlet->employeePasar->pasar->name) ? $val->outlet->employeePasar->pasar->name : ""),
                        'Tanggal'   => (isset($val->date) ? $val->date : ""),
                        'Outlet'    => (isset($val->outlet->name) ? $val->outlet->name : "-")
                    );
                    $getId = array_column(\App\SalesMdDetail::get(['id_product'])->toArray(),'id_product');
                    $productList = \App\Product::whereIn('id', $getId)->get();
                    foreach ($productList as $pro) {
                        $data[$key][$pro->name] = "-";
                    }
                    foreach ($detail as $det) {
                        $data[$key][$det->product->name] = $det->qty_actual." ".$det->satuan;
                    }
                }
            }
        
            $filename = "ReportSalesMD".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('SalesMdPasar', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal Unduh!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }


    public function SMDstockist(Request $request)
    {
        $stock = StockMD::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $stock->whereHas('employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
         if ($request->has('periode')) {
            $stock->whereMonth('date', substr($request->input('periode'), 0, 2));
            $stock->whereYear('date', substr($request->input('periode'), 3));
        }  
        if ($request->has('pasar')) {
            $stock->whereHas('pasar', function($q) use ($request){
                return $q->where('id_pasar', $request->input('pasar'));
            });
        } 
        $data = array();
        $id = 1;
        foreach ($stock->get() as $val) {
            if ($val->employee->position->level == 'mdgtc'){
                $data[] = array(
                    'id'        => $id++,
                    'id_stock'  => $val->id,
                    'name'      => (isset($val->employee->name) ? $val->employee->name : ""),
                    'pasar'     => (isset($val->pasar->name) ? $val->pasar->name : ""),
                    'tanggal'   => (isset($val->date) ? $val->date : ""),
                    'week'      => (isset($val->week) ? $val->week : ""),
                    'stockist'  => (isset($val->stockist) ? $val->stockist : "")
                );
            }
        }

        $getId = array_column(\App\StockMdDetail::get(['id_product'])->toArray(),'id_product');
        $product = \App\Product::whereIn('id', $getId)->get();

        $dt = Datatables::of(collect($data));
        foreach ($product as $pdct) {
            $dt->addColumn('product-'.$pdct->id, function($stock) use ($pdct) {
                $oos = \App\StockMdDetail::where([
                    'id_stock' => $stock['id_stock'],
                    'id_product' => $pdct->id
                ])->first();
                return (isset($oos['oos']) ? $oos['oos'] : "-");
            });
        }
        return $dt->make(true);
    }

    public function exportSMDstocking()
    {
        $stock = StockMD::whereMonth('date', Carbon::now()->month);
        if ($stock->count() > 0) {
            foreach ($stock->get() as $key => $val) {
                if ($val->employee->position->level == 'mdgtc') {
                    $detail = StockMdDetail::where('id_stock',$val->id)->get();
                    $data[] = array(
                        'Name'      => (isset($val->employee->name) ? $val->employee->name : "-"),
                        'Pasar'     => (isset($val->pasar->name) ? $val->pasar->name : "-"),
                        'Date'      => (isset($val->date) ? $val->date : ""),
                        'Week'      => (isset($val->week) ? $val->week : ""),
                        'Stockist'  => (isset($val->stockist) ? $val->stockist : "-")
                    );
                    $getId = array_column(\App\StockMdDetail::get(['id_product'])->toArray(),'id_product');
                    $productList = \App\Product::whereIn('id', $getId)->get();
                    foreach ($productList as $pro) {
                        $data[$key][$pro->name] = "-";
                    }
                    foreach ($detail as $det) {
                        $data[$key][$det->product->name] = $det->oos;
                    }
                }
            }
            $filename = "ReportSMDStokist".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('ReportSMDStokist', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal Unduh!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function SMDcbd(Request $request)
    {
        $cbd = Cbd::orderBy('created_at', 'DESC')->with(['employee','outlet'])
        ->when($request->has('employee'), function ($q) use ($request){
            return $q->whereIdEmployee($request->input('employee'));
        })
        ->when($request->has('periode'), function ($q) use ($request){
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->when($request->has('outlet'), function ($q) use ($request){
            return $q->where('id_outlet', $request->input('outlet'));
        })->get();

        $data = array();
        $id = 1;
        foreach ($cbd as $val) {
            if ($val->employee->position->level == 'mdgtc'){
                $data[] = array(
                    'id'            => $id++,
                    'outlet'        => $val->outlet->name,
                    'employee'      => $val->employee->name,
                    'date'          => $val->date,
                    'photo'         => (isset($val->photo) ? "<a href=".asset('/uploads/cbd/'.$val->photo)." class='btn btn-sm btn-success btn-square popup-image' title=''><i class='si si-picture mr-2'></i> View Photo</a>" : "-"),
                );
            }
        }

        $dt = Datatables::of(collect($data));
        
        return $dt->rawColumns(['photo'])->make(true);
    }

    public function SMDNewCbd(Request $request)
    {
        $cbd = NewCbd::orderBy('created_at', 'DESC')->with(['employee','outlet'])
        ->when($request->has('employee'), function ($q) use ($request){
            return $q->whereIdEmployee($request->input('employee'));
        })
        ->when($request->has('periode'), function ($q) use ($request){
            return $q->whereMonth('date', substr($request->input('periode'), 0, 2))
            ->whereYear('date', substr($request->input('periode'), 3));
        })
        ->when($request->has('outlet'), function ($q) use ($request){
            $q->whereHas('outlet', function($q2) use ($request){
                return $q2->where('id_outlet', $request->input('outlet'));
            });
        })->get();

        $data = array();
        $id = 1;
        foreach ($cbd as $val) {
            if ($val->employee->position->level == 'mdgtc'){
                $data[] = array(
                    'id'            => $id++,
                    'outlet'        => $val->outlet->name,
                    'employee'      => $val->employee->name,
                    'date'          => $val->date,
                    'photo'         => (isset($val->photo) ? "<a href=".asset('/uploads/cbd/'.$val->photo)." class='btn btn-sm btn-success btn-square popup-image' title=''><i class='si si-picture mr-2'></i> View Photo</a>" : "-"),
                    'photo2'         => (isset($val->photo2) ? "<a href=".asset('/uploads/cbd/'.$val->photo2)." class='btn btn-sm btn-success btn-square popup-image' title=''><i class='si si-picture mr-2'></i> View Photo</a>" : "-"),
                    'posm_shop_sign'       => ($val->posm_shop_sign == 1) ? 'Yes' : 'No',
                    'posm_hangering_mobile'=> ($val->posm_hangering_mobile == 1) ? 'Yes' : 'No',
                    'posm_poster'          => ($val->posm_poster == 1) ? 'Yes' : 'No',
                    'posm_others'          => $val->posm_others?? '-',
                    'cbd_competitor'=> $val->cbd_competitor,
                    'cbd_position'  => $val->cbd_position,
                    'outlet_type'   => $val->outlet_type,
                    'total_hanger'  => $val->total_hanger,
                );
            }
        }

        $dt = Datatables::of(collect($data));
        
        return $dt->rawColumns(['photo','photo2'])->make(true);
    }

    public function getCbd($data, $periode, $day)
    {
        $date = Carbon::now()->format('Y-m-').$day;
        $cbd = \App\Cbd::where([
            'id_employee' => $data['id_employee'],
            'date' => $date
        ])->count();
        return $cbd;
    }

    public function getStockist($data, $periode, $day)
    {
        $year = substr($periode, 3);
        $month = substr($periode, 0,2);
        $date = Carbon::parse($year."-".$month."-".$day);
        $stock = StockMD::where([
            'id_pasar' => $data['id_pasar'],
            'date' => $date
        ])->first();
        return (isset($stock->stockist) ? $stock->stockist : "Tidak ada");
    }

    public function getCall($data, $periode, $day)
    {
        $year = substr($periode, 3);
        $month = substr($periode, 0,2);
        $date = Carbon::parse($year."-".$month."-".$day);
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

    public function getRo($data, $periode, $day)
    {
        $year = substr($periode, 3);
        $month = substr($periode, 0,2);
        $date = Carbon::parse($year."-".$month."-".$day);
        $ro = Outlet::where([
            'id_employee_pasar' => $data['id'],
            'active' => true
        ])->whereRaw("DATE(created_at) > '".$date."'");
        return $ro->count();
    }

    public function exportSmdDist()
    {
        $dist = Distribution::whereMonth('date',Carbon::now()->month);
        if ($dist->count() > 0) {
		    foreach ($dist->get() as $key => $val) {
                if($val->employee->position->level == 'mdgtc') {
                    $detail = DistributionDetail::where('id_distribution',$val->id)->get();
		    	    $data[] = array(
                        'Employee'  => $val->employee->name,
                        'Pasar'     => (isset($val->outlet->employeePasar->pasar->name) ? $val->outlet->employeePasar->pasar->name : ""),
                        'Tanggal'   => (isset($val->date) ? $val->date : ""),
                        'Outlet'    => (isset($val->outlet->name) ? $val->outlet->name : "-")
                    );
                }
            }

            $getId = array_column(\App\DistributionDetail::get(['id_product'])->toArray(),'id_product');
                $productList = \App\Product::whereIn('id', $getId)->get();
                foreach ($productList as $pro) {
                    $data[$key][$pro->name] = "-";
                }
                foreach ($detail as $det) {
                    $data[$key][$det->product->name] = $det->qty_actual." ".$det->satuan;
                }
        
		    $filename = "ReportDistPf".Carbon::now().".xlsx";
		    return Excel::create($filename, function($excel) use ($data) {
		    	$excel->sheet('ReportDistPf', function($sheet) use ($data)
		    	{
		    		$sheet->fromArray($data);
		    	});
            })->download();
        } else {
            return redirect()->back()
            ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal Unduh!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function exportAttandance()
    {
        $employee = AttendanceOutlet::whereMonth('checkin', Carbon::now()->month);
        if ($employee->count() > 0) {
		    foreach ($employee->get() as $val) {
                if ($val->attendance->employee->position->level == 'mdgtc') {
		    	    $data[] = array(
                        'region'    => (isset($val->outlet->employeePasar->pasar->name) ? $val->outlet->employeePasar->pasar->name : ""),
                        'area'      => (isset($val->outlet->employeePasar->pasar->subarea->area->name) ? $val->outlet->employeePasar->pasar->subarea->area->name : ""),
                        'subarea'   => (isset($val->outlet->employeePasar->pasar->subarea->name) ? $val->outlet->employeePasar->pasar->subarea->name : ""),
                        'nama'      => (isset($val->attendance->employee->name) ? $val->outlet->employee->name : ""),
                        'jabatan'   => $val->attendance->employee->position->name,
                        'pasar'     => (isset($val->outlet->employeePasar->pasar->name) ? $val->outlet->employeePasar->pasar->name : ""),
                        'outlet'    => (isset($val->outlet->name) ? $val->outlet->name : "-"),
                        'tanggal'   => Carbon::parse($val->checkin)->day,
                        'checkin'   => Carbon::parse($val->checkin)->format('H:m:s'),
                        'checkout'  => ($val->checkout ? Carbon::parse($val->checkout)->format('H:m:s') : "Belum Check-out")
                    );
                }
            }
        
		    $filename = "AttandanceReport".Carbon::now().".xlsx";
		    return Excel::create($filename, function($excel) use ($data) {
		    	$excel->sheet('AttandanceReport', function($sheet) use ($data)
		    	{
		    		$sheet->fromArray($data);
		    	});
            })->download();
        } else {
            return redirect()->back()
            ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal Unduh!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
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


    // ************ SPG PASAR ************ //
    public function SPGsales(Request $request)
    {
        $sales = SalesSpgPasar::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $sales->whereHas('employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
        if ($request->has('pasar')) {
            $sales->whereHas('pasar', function($q) use ($request){
                return $q->where('id_pasar', $request->input('pasar'));
            });
        } 
        if ($request->has('periode')) {
            $sales->whereMonth('date', substr($request->input('periode'), 0, 2));
            $sales->whereYear('date', substr($request->input('periode'), 3));
        }
        $data = array();
        $product = array();
        $id = 1;
        foreach ($sales->get() as $key => $value) {
            if ($value->employee->position->level = 'spggtc') {
                $data[] = array(
                    'id' => $id++,
                    'id_pasar' => $value->id_pasar,
                    'date' => $value->date,
                    'id_employee' => $value->id_employee,
                    'nama_spg' => $this->isset($value->employee->name),
                    'pasar' => $this->isset($value->pasar->name),
                    'tanggal' => $this->isset($value->date),
                    'nama' => $this->isset($value->name),
                    'phone' => $this->isset($value->phone)
                );
            }
        }
        $getId = array_column(\App\SalesSpgPasarDetail::get(['id_product'])->toArray(),'id_product');
        $product = \App\Product::whereIn('id', $getId)->get();
        $dt = Datatables::of(collect($data));
        $columns = array();
        foreach ($product as $pdct) {
            $columns[] = 'product-'.$pdct->id;
            $dt->addColumn('product-'.$pdct->id, function($sales) use ($pdct) {
                $sale = SalesSpgPasar::where([
                    'id_employee' => $sales['id_employee'],
                    'id_pasar' => $sales['id_pasar'],
                    'date' => $sales['date'],
                ])->get(['id'])->toArray();
                $getIdSale = array_column($sale,'id');
                $detail = \App\SalesSpgPasarDetail::whereIn('id_sales', $getIdSale)
                ->where('id_product', $pdct['id'])
                ->get();
                $satuan = array();
                foreach ($detail as $value) {
                     $satuan[] = $value->qty_actual."&nbsp;".$value->satuan;
                }
                return implode(", ", $satuan);
            });
        }
        $dt->rawColumns($columns);
        return $dt->make(true);
    }

    // EXPORT SPG
    public function exportSpgSales()
    {
        $sales = SalesSpgPasar::whereMonth('date', Carbon::now()->month);
        if ($sales->count() > 0) {
            $product = array();
            foreach ($sales->get() as $key => $value) {
                if ($value->employee->position->level == 'spggtc') {
                    $detail = SalesSpgPasarDetail::where('id_sales',$value->id)->get();
                    $data[] = array(
                        'Nama SPG'              => (isset($value->employee->name) ? $value->employee->name : ""),
                        'Pasar'                 => (isset($value->pasar->name) ? $value->pasar->name : ""),
                        'Date'                  => (isset($value->date) ? $value->date  : ""),
                        'Nama Pemilik Pasar'    => (isset($value->name) ? $value->name : ""),
                        'Phone Pemilik Pasar'   => (isset($value->phone) ? $value->phone : "")
                    );
                    $getId = array_column(\App\SalesSpgPasarDetail::get(['id_product'])->toArray(),'id_product');
                    $productList = \App\Product::whereIn('id', $getId)->get();
                    foreach ($productList as $pro) {
                        $data[$key][$pro->name] = "-";
                    }
                    foreach ($detail as $det) {
                        $data[$key][$det->product->name] = $det->qty_actual." ".$det->satuan;
                    }
                }
            }
            $filename = "AttandanceSPGReport".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('AttandanceSPGReport', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }


    public function SPGrekap(Request $request)
    {
        $rekap = SalesRecap::orderBy('created_at', 'DESC');
        if ($request->has('employee')) {
            $rekap->whereHas('employee', function($q) use ($request){
                return $q->where('id_employee', $request->input('employee'));
            });
        } 
        if ($request->has('periode')) {
            $rekap->whereMonth('date', substr($request->input('periode'), 0, 2));
            $rekap->whereYear('date', substr($request->input('periode'), 3));
        }
        $id = 1;
        $data = array();
        foreach ($rekap->get() as $val) {
            if ($val->employee->position->level == 'spggtc') {
                $data[] = array(
                    'id' => $id++,
                    'name' => (isset($val->employee->name) ? $val->employee->name : "-"),
                    'outlet' => (isset($val->outlet->name) ? $val->outlet->name : "-"),
                    'date' => (isset($val->date) ? $val->date : "-"),
                    'total_buyer' => (isset($val->total_buyer) ? $val->total_buyer : "-"),
                    'total_sales' => (isset($val->total_sales) ? $val->total_sales : "-"),
                    'total_value' => (isset($val->total_value) ? $val->total_value : "-"),
                    'photo' => (isset($val->photo) ? $val->photo : "-")
                );
            }
        }
        return Datatables::of(collect($data))
        ->addColumn('action', function($stock) {
            if ($stock['photo'] != "-") {
                $img_url = asset('/uploads/sales_recap')."/".$stock['photo'];
                $oos = "<img src='".$img_url."' width='50px'/>";
            } else {
                $img_url = asset('/')."no-image.jpg";
                $oos = "<img src='".$img_url."' width='50px'/>";
            }
            return $oos;
        })->make(true);
    }

    public function exportSPGrekap()
    {
        $rekap = SalesRecap::whereMonth('date', Carbon::now()->month);
        if ($rekap->count() > 0) {
            foreach ($rekap->get() as $val) {
                if ($val->employee->position->level == 'spggtc') {
                    $data[] = array(
                        'Name' => (isset($val->employee->name) ? $val->employee->name : "-"),
                        'Outlet' => (isset($val->outlet->name) ? $val->outlet->name : "-"),
                        'Date' => (isset($val->date) ? $val->date : "-"),
                        'Total Buyer' => (isset($val->total_buyer) ? $val->total_buyer : "-"),
                        'Total Sales' => (isset($val->total_sales) ? $val->total_sales : "-"),
                        'Total Value' => (isset($val->total_value) ? $val->total_value : "-")
                    );
                }
            }
            $filename = "SPGRekap".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('SPGReakp', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function SPGattendance(Request $request)
    {
        $employee = AttendancePasar::whereHas('attendance.employee', function($query) use ($request){
            return $query->where('id_employee', $request->input('employee'));
        })->whereMonth('checkin', substr($request->input('periode'), 0, 2))
        ->whereYear('checkin', substr($request->input('periode'), 3))->get();
        $data = array();
        $absen = array();
        $id = 1;
        foreach ($employee as $val) {
            if ($val->attendance->employee->position->level == 'spggtc') {
                $checkin = Carbon::parse($val->checkin)->setTimezone($val->attendance->employee->timezone->timezone)->format('H:i:s');
                $checkout = ($val->checkout ? Carbon::parse($val->checkout)->setTimezone($val->attendance->employee->timezone->timezone)->format('H:i:s') : "Belum Check-out");
                $data[] = array(
                    'id' => $id++,
                    'area' => $this->isset($val->pasar->subarea->area->name),
                    'subarea' => $this->isset($val->pasar->subarea->name),
                    'nama' => $this->isset($val->attendance->employee->name),
                    'jabatan' => $this->isset($val->attendance->employee->position->name),
                    'pasar' => $this->isset($val->pasar->name),
                    'tanggal' => Carbon::parse($val->checkin)->day,
                    'checkin' => $checkin." ".$val->attendance->employee->timezone->name,
                    'checkout' => $checkout." ".$val->attendance->employee->timezone->name
                );
            }
        }
        return Datatables::of(collect($data))->make(true);
    }

    public function exportSpgAttandance()
    {
        $employee = AttendancePasar::whereMonth('checkin', Carbon::now()->month);
        if ($employee->count() > 0) {
            foreach ($employee->get() as $val) {
                if($val->attendance->employee->position->level == 'spggtc') {
                    $data[] = array(
                    'area'      => (isset($val->pasar->subarea->area->name) ? $val->pasar->subarea->area->name : ""),
                    'subarea'   => (isset($val->pasar->subarea->name) ?  $val->pasar->subarea->area->name : ""),
                    'nama'      => (isset($val->attendance->employee->name) ? $val->attendance->employee->name : ""),
                    'jabatan'   => (isset($val->attendance->employee->position->name) ? $val->attendance->employee->position->name : ""),
                    'pasar'     => (isset($val->pasar->name) ? $val->pasar->name : ""),
                    'tanggal'   => Carbon::parse($val->checkin)->day,
                    'checkin'   => Carbon::parse($val->checkin)->format('H:m:s'),
                    'checkout'  => ($val->checkout ? Carbon::parse($val->checkout)->format('H:m:s') : "Belum Check-out")
                    );
                }
            }
        
            $filename = "AttandanceSPGReport".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('AttandanceSPGReport', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal Unduh!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function SPGsalesSummary(Request $request)
    {
        // return $request->all();

        $periode = Carbon::parse($request->periode)->format('Y-m-d');
        
        $products = ProductFokusSpg::whereHas('product', function($query) use ($request){
                        return $query->where('id_subcategory', $request->id_subcategory);
                    })->whereDate('from', '<=', $periode)->whereDate('to', '>=', $periode)->get();        

        $sub_cat = array_unique($products->pluck('product.subcategory.id')->toArray());

        $sales = SalesSpgPasarSummary::whereHas('detailSales.product.subcategory', function ($query) use ($request, $sub_cat){
                                        return $query->where('id', $request->id_subcategory)->whereIn('id', $sub_cat);
                                     })
                                     ->whereMonth('date', Carbon::parse($request->periode)->month)
                                     ->whereYear('date', Carbon::parse($request->periode)->year)
                                     ->groupBy('id_employee', 'id_pasar', 'date')
                                     ->orderBy('date', 'DESC')
                                     ->orderBy('id_employee', 'ASC')
                                     ->orderBy('id_pasar', 'ASC');

        // return $sales->first()->getProductsValue();
        
        $dt = Datatables::of($sales);

        /* SALES PER PRODUCT(S) */
        foreach ($products as $column) {
            $dt->addColumn('product_'.$column->product->id, function($item) use ($column) {
                // return $item->detail;
                return array_key_exists($column->product->id, $item->detail) ? number_format($item->detail[$column->product->id]) : 0;
            });
        }

        /* SALES OTHER, SALES PF, TOTAL VALUE */
        $dt->addColumn('sales_other', function($item) {
            return number_format($item->sales_other);
        });
        $dt->addColumn('sales_pf', function($item) {
            return number_format($item->sales_pf);
        });
        $dt->addColumn('total_value', function($item) {
            return number_format($item->total_value);
        });

        return $dt->make(true);
    }    

    public function SPGsalesSummaryHeader(Request $request){

        // return $request->all();

        $periode = Carbon::parse($request->periode)->format('Y-m-d');
        
        $products = ProductFokusSpg::whereHas('product', function($query) use ($request){
                        return $query->where('id_subcategory', $request->id_subcategory);
                    })->whereDate('from', '<=', $periode)->whereDate('to', '>=', $periode)->get();

        $sub_category = SubCategory::where('id', $request->id_subcategory)->first()->name;

        $th = "";
        $array_column = array();

        foreach ($products as $item) {
            $th .= "<th>Sales ".$item->product->name."</th>";
            array_push($array_column, ['data'=>'product_'.$item->product->id, 'name'=>'product_'.$item->product->id ]);
            // array_push($array_column, $item->id);
        }

        $th .= "<th>Sales Other</th><th>Sales Product Fokus</th><th>Total Value</th>";
        array_push($array_column, 
            ['data'=>'sales_other', 'name'=>'sales_other'],
            ['data'=>'sales_pf', 'name'=>'sales_pf'],
            ['data'=>'total_value', 'name'=>'total_value']
        );

        return 
        [
            "th" => $th,
            "columns" => $array_column
        ];
    }

    public function SPGsalesAchievement_exportXLS()
    {
        $result = DB::transaction(function(){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "SPG Pasar - Report Achievement (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportSPGPasarAchievementJob($JobTrace, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function SPGsalesAchievement()
    {
        $sales = SalesSpgPasarAchievement::whereNull('deleted_at')
                              ->groupBy(DB::raw("CONCAT_WS('-',MONTH(date),YEAR(date))"), DB::raw('id_employee'))
                              ->orderBy(DB::raw("CONCAT_WS('-',MONTH(date),YEAR(date))"), 'ASC')
                              ->orderBy('id_employee', 'ASC');

        // return $sales->get();
        
        return Datatables::of($sales)
        // ->addColumn('periode', function ($data) {
        //     return Carbon::parse($data->date)->format('F Y');
        // })
        // ->addColumn('area', function ($data) {
        //     return $data->employee->getAreaByPasar();
        // })
        // ->addColumn('nama_spg', function ($data) {
        //     return $data->employee->name;
        // })
        // ->addColumn('hk', function ($data) {
        //     // return $data->getHk();
        // })
        // ->addColumn('sum_of_jumlah', function ($data) {
        //     return $data->getSumOfJumlah();
        // })
        // ->addColumn('sum_of_pf', function ($data) {
        //     return $data->getSumOfPfValue();
        // })
        // ->addColumn('sum_of_total', function ($data) {
        //     return $data->getSumOfTotalValue();
        // })
        // ->addColumn('eff_kontak', function ($data) {
        //     return $data->getSumEffKontak();
        // })
        // ->addColumn('act_value', function ($data) {
        //     return 'TEST';
        // })
        // ->addColumn('sales_per_kontak', function ($data) {
        //     return 'TEST';
        // })
        ->make(true);
        // return Datatables::of($sales)->make(true);
    }

    public function SMDsalesSummaryHeader(Request $request){

        // return $request->all();

        $periode = Carbon::parse($request->periode)->format('Y-m-d');

        // $id_subcategories = array_unique(FokusProduct::whereHas('pf.Fokus.channel', function ($query){
        //                     return $query->where('name', 'GTC');
        //                 })
        //                 ->whereHas('pf', function ($query) use ($periode){
        //                     return $query->whereDate('from', '<=', $periode)
        //                                  ->whereDate('to', '>=', $periode);
        //                 })                        
        //                 ->get()->pluck('product.subcategory.id')->toArray());

        $id_subcategories = array_unique(ProductFokusGtc::whereDate('from', '<=', $periode)->whereDate('to', '>=', $periode)->get()->pluck('product.subcategory.id')->toArray());

        $subcategories = SubCategory::whereIn('id', $id_subcategories)->get();
        
        $th_before = "";
        $th = "";
        $array_column = array();        

        /* DISTRIBUSI */

        $colspan_dist = 0;
        foreach ($subcategories as $item) {
            $th .= "<th>Dist. ".$item->name."</th>";
            array_push($array_column, ['data'=>'dist_'.$item->id, 'name'=>'dist_'.$item->id ]);
            $colspan_dist += 1;
        }
        if($colspan_dist > 0) $th_before .= "<th colspan='".$colspan_dist."' style='text-align: center;'>Distribusi Produk Fokus</th>";

        /* SALES */

        $colspan_sales = 0;
        foreach ($subcategories as $item) {
            $th .= "<th>Sales ".$item->name."</th>";
            array_push($array_column, ['data'=>'sales_'.$item->id, 'name'=>'sales_'.$item->id ]);
            $colspan_sales += 1;
        }
        if($colspan_sales > 0) $th_before .= "<th colspan='".$colspan_sales."' style='text-align: center;'>Sales [ Unit ] / Pack</th>";

        /* EC, VALUE PF, VALUE NON, TOTAL */

        $th_before .= "<th colspan='5' style='text-align: center;'>SUMMARY</th>";
        $th .= "<th>EC</th><th>Value Product Fokus</th><th>Value Non Produk Fokus</th><th>Value Total</th><th>CBD</th>";
        array_push($array_column, 
            ['data'=>'eff_call', 'name'=>'eff_call'],
            ['data'=>'value_pf', 'name'=>'value_pf'],
            ['data'=>'value_non_pf', 'name'=>'value_non_pf'],
            ['data'=>'value_total', 'name'=>'value_total'],
            ['data'=>'cbd', 'name'=>'cbd']
        );

        /* OOS */

        $id_product_oos = StockMdDetail::whereHas('stock', function ($query) use ($periode){
                                return $query->whereMonth('date', Carbon::parse($periode)->month)->whereYear('date', Carbon::parse($periode)->year);
                            })->pluck('id_product')->toArray();

        $products = Product::whereIn('id', $id_product_oos)->get();

        $colspan_oos = 0;
        foreach ($products as $item) {
            $th .= "<th>".$item->name."</th>";
            array_push($array_column, ['data'=>'oos_'.$item->id, 'name'=>'oos_'.$item->id ]);
            $colspan_oos += 1;
        }
        if($colspan_oos > 0) $th_before .= "<th colspan='".$colspan_oos."' style='text-align: center;'>OOS (STOKIES)</th>";

        return 
        [
            "th_before" => $th_before,
            "th" => $th,
            "columns" => $array_column
        ];

        // return array_unique($id_subcategories);
        
        // $products = ProductFokusSpg::whereHas('product', function($query) use ($request){
        //                 return $query->where('id_subcategory', $request->id_subcategory);
        //             })->whereDate('from', '<=', $periode)->whereDate('to', '>=', $periode)->get();

        // $sub_category = SubCategory::where('id', $request->id_subcategory)->first()->name;

        // $th = "";
        // $array_column = array();

        // foreach ($products as $item) {
        //     $th .= "<th>Sales ".$item->product->name."</th>";
        //     array_push($array_column, ['data'=>'product_'.$item->product->id, 'name'=>'product_'.$item->product->id ]);
        //     // array_push($array_column, $item->id);
        // }

        // $th .= "<th>Sales Other</th><th>Sales Product Fokus</th><th>Total Value</th>";
        // array_push($array_column, 
        //     ['data'=>'sales_other', 'name'=>'sales_other'],
        //     ['data'=>'sales_pf', 'name'=>'sales_pf'],
        //     ['data'=>'total_value', 'name'=>'total_value']
        // );

        // return 
        // [
        //     "th" => $th,
        //     "columns" => $array_column
        // ];
    }

    public function SMDsalesSummary(Request $request)
    {
        // return $request->all();

        $periode = Carbon::parse($request->periode)->format('Y-m-d');

        // $id_subcategories = array_unique(FokusProduct::whereHas('pf.Fokus.channel', function ($query){
        //                     return $query->where('name', 'GTC');
        //                 })
        //                 ->whereHas('pf', function ($query) use ($periode){
        //                     return $query->whereDate('from', '<=', $periode)
        //                                  ->whereDate('to', '>=', $periode);
        //                 })                        
        //                 ->get()->pluck('product.subcategory.id')->toArray());

        $id_subcategories = array_unique(ProductFokusGtc::whereDate('from', '<=', $periode)->whereDate('to', '>=', $periode)->get()->pluck('product.subcategory.id')->toArray());

        $id_product_oos = StockMdDetail::whereHas('stock', function ($query) use ($periode){
                                return $query->whereMonth('date', Carbon::parse($periode)->month)->whereYear('date', Carbon::parse($periode)->year);
                            })->pluck('id_product')->toArray();
        
        $sales = SalesMdSummary::whereMonth('date', Carbon::parse($request->periode)->month)
                                 ->whereYear('date', Carbon::parse($request->periode)->year)
                                 ->groupBy('id_employee', 'date')
                                 ->orderBy('date', 'DESC')
                                 ->orderBy('id_employee', 'ASC');
                                 // ->orderBy('outlets.id_pasar', 'ASC');

        // return $sales->get();
        
        $dt = Datatables::of($sales);

        /* DISTRIBUTION PF */
        foreach ($id_subcategories as $column) {
            $dt->addColumn('dist_'.$column, function($item) use ($column) {
                // return $item->detail;
                return array_key_exists($column, $item->distribusi_pf) ? number_format($item->distribusi_pf[$column]) : 0;
            });
        }

        /* SALES PF */
        foreach ($id_subcategories as $column) {
            $dt->addColumn('sales_'.$column, function($item) use ($column) {
                // return $item->detail;
                return array_key_exists($column, $item->sales_pf) ? number_format($item->sales_pf[$column]) : 0;
            });
        }

        /* OOS */
        foreach ($id_product_oos as $column) {
            $dt->addColumn('oos_'.$column, function($item) use ($column) {
                // return $item->detail;
                return array_key_exists($column, $item->oos) ? number_format($item->oos[$column]) : 0;
            });
        }

        /* VALUE PF, VALUE NON PF, TOTAL VALUE */
        $dt->addColumn('value_pf', function($item) {
            return number_format($item->value_pf);
        });
        $dt->addColumn('value_non_pf', function($item) {
            return number_format($item->value_non_pf);
        });
        $dt->addColumn('value_total', function($item) {
            return number_format($item->value_total);
        });

        return $dt->make(true);
    }

    public function SMDsalesSummaryExportXLS($filterPeriode)
    {
        $result = DB::transaction(function() use ($filterPeriode){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "SMD Pasar - Report Sales Summary " . Carbon::parse($filterPeriode)->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportSMDReportSalesSummaryJob($JobTrace, [$filterPeriode, $filecode]));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function SMDTargetKpi(Request $request)
    {
        $target_kpi = TargetKpiMd::whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        });

        // return is_null($target_kpi->first()->getTarget($request->periode)) ? 0 : $target_kpi->first()->getTarget($request->periode)['hk'];

        // return array_key_exists('hk', $target_kpi->first()->getTarget($request->periode)) ? $target_kpi->first()->getTarget($request->periode)['hk'] : 0;
        // return $request->all();
        
        return Datatables::of($target_kpi)
        ->addColumn('hk_target', function ($item) use ($request){
            return is_null($item->getTarget($request->periode)) ? 0 : $item->getTarget($request->periode)['hk'];
        })
        ->addColumn('target_sales_value', function ($item) use ($request) {
            return number_format(is_null($item->getTarget($request->periode)) ? 0 : $item->getTarget($request->periode)['value_sales']);
        })
        ->addColumn('target_ec_pf', function ($item) use ($request) {
            return is_null($item->getTarget($request->periode)) ? 0 : $item->getTarget($request->periode)['ec'];
        })
        ->addColumn('target_cbd', function ($item) use ($request) {
            return is_null($item->getTarget($request->periode)) ? 0 : $item->getTarget($request->periode)['cbd'];
        })
        ->addColumn('ach_sales_value', function ($item) use ($request) {
            return number_format(@$item->getSalesValue($request->periode));
        })
        ->addColumn('ach_ec_pf', function ($item) use ($request) {
            return number_format(@$item->getEc($request->periode));
        })
        ->addColumn('ach_cbd', function ($item) use ($request) {
            return number_format(@$item->getCbd($request->periode));
        })
        ->make(true);

    }

    public function SMDTargetKpiExportXLS($filterPeriode)
    {
        $result = DB::transaction(function() use ($filterPeriode){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "SMD Pasar - Report Target KPI " . Carbon::parse($filterPeriode)->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportSMDReportTargetKPIJob($JobTrace, $filterPeriode, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function SMDKpi(Request $request)
    {
        $target_kpi = TargetKpiMd::whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        });

        // return is_null($target_kpi->first()->getTarget($request->periode)) ? 0 : $target_kpi->first()->getTarget($request->periode)['hk'];

        // return array_key_exists('hk', $target_kpi->first()->getTarget($request->periode)) ? $target_kpi->first()->getTarget($request->periode)['hk'] : 0;
        // return $request->all();
        
        return Datatables::of($target_kpi)
        ->addColumn('hk_target', function ($item) use ($request){
            return is_null($item->getTarget($request->periode)) ? 0 : $item->getTarget($request->periode)['hk'];
        })
        ->addColumn('hk_actual', function ($item) use ($request){
            return @$item->getHkActual($request->periode);
        })
        ->addColumn('sum_of_cbd', function ($item) use ($request){
            return @$item->getCbd($request->periode);
        })
        ->addColumn('sum_of_call', function ($item) use ($request){
            return @$item->getCall($request->periode);
        })
        ->addColumn('sum_of_ec', function ($item) use ($request){
            return @$item->getEc($request->periode);
        })
        ->addColumn('sum_cat_1', function ($item) use ($request){
            return @$item->getSumCat1($request->periode);
        })
        ->addColumn('sum_cat_2', function ($item) use ($request){
            return @$item->getSumCat2($request->periode);
        })
        ->addColumn('sum_of_total_value', function ($item) use ($request){
            return number_format(@$item->getTotalValue($request->periode));
        })
        ->addColumn('sum_of_value_pf', function ($item) use ($request){
            return number_format(@$item->getSalesValue($request->periode));
        })
        ->addColumn('average_cbd', function ($item) use ($request){
            return round(@$item->getAvgCbd($request->periode));
        })
        ->addColumn('average_call', function ($item) use ($request){
            return round(@$item->getAvgCall($request->periode));
        })
        ->addColumn('average_ec', function ($item) use ($request){
            return round(@$item->getAvgEc($request->periode));
        })
        ->addColumn('average_cat_1', function ($item) use ($request){
            return @$item->getAvgCat1($request->periode);
        })
        ->addColumn('average_cat_2', function ($item) use ($request){
            return @$item->getAvgCat2($request->periode);
        })
        ->addColumn('average_of_total_value', function ($item) use ($request){
            return number_format(@$item->getAvgTotalValue($request->periode));
        })
        ->addColumn('average_of_value_pf', function ($item) use ($request){
            return number_format(@$item->getAvgSalesValue($request->periode));
        })
        ->addColumn('best_cbd', function ($item) use ($request){
            return @$item->getBestCbd($request->periode);
        })
        ->addColumn('best_call', function ($item) use ($request){
            return @$item->getBestCall($request->periode);
        })
        ->addColumn('best_ec', function ($item) use ($request){
            return @$item->getBestEc($request->periode);
        })
        ->addColumn('best_cat_1', function ($item) use ($request){
            return @$item->getBestCat1($request->periode);
        })
        ->addColumn('best_cat_2', function ($item) use ($request){
            return @$item->getBestCat2($request->periode);
        })
        ->addColumn('best_of_total_value', function ($item) use ($request){
            return @$item->getBestTotalValue($request->periode);
        })
        ->addColumn('best_of_value_pf', function ($item) use ($request){
            return @$item->getBestSalesValue($request->periode);
        })
        ->addColumn('total_point', function ($item) use ($request){
            return @$item->getTotalPoint($request->periode);
        })     
        ->make(true);

    }

    public function SMDKpiExportXLS($filterPeriode)
    {
        $result = DB::transaction(function() use ($filterPeriode){
            try
            {
                $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                $JobTrace = JobTrace::create([
                    'id_user' => Auth::user()->id,
                    'date' => Carbon::now(),
                    'title' => "SMD Pasar - Report KPI " . Carbon::parse($filterPeriode)->format("F Y") ." (" . $filecode . ")",
                    'status' => 'PROCESSING',
                ]);
                dispatch(new ExportSMDReportKPIJob($JobTrace, $filterPeriode, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

    public function SMDCat1Cat2(Request $request){

        $pf = Pf::whereDate('from', '<=', Carbon::parse($request->periode))
                ->whereDate('to', '>=', Carbon::parse($request->periode))
                ->first();

        return [
            "cat1" => @$pf->category1->name,
            "cat2" => @$pf->category2->name
        ];

    }
}
