<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailIn;
use App\SellIn;
use App\SellInSummary;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use App\StoreDistributor;
use App\Distributor;
use App\Filters\SummaryFilters;
use App\Helper\ReportHelper as ReportHelper;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

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

        $data = SellInSummary::filter($filters);

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


    // *********** STOCK ****************** //


    // *********** EXPORTING ****************** //

    public function tes(){

        $list = collect([
            [ 'id' => 1, 'name' => 'Jane' ],
            [ 'id' => 2, 'name' => 'John' ],
        ]);

        $data = SellInSummary::where('id', '!=', 0);

        $sql = $data->toSql();
        $bindings = $data->getBindings();

        $sql = $data->toSql();
        $bindings = $data->getBindings();

        $data3 = collect(DB::select($sql, $bindings));

        return (new FastExcel($this->reportHelper->mapForExportSalesNew($data3)))->download('file.xlsx');

        // return redirect()->back();

    }

    public function export(Request $request, SummaryFilters $filters){

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

}
