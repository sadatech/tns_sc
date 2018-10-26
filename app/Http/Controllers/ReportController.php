<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailIn;
use App\SellIn;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use App\StoreDistributor;
use App\Distributor;

class ReportController extends Controller
{
    // *********** SELL IN ****************** //

	public function sellInIndex(){
		return view('report.sellin');
	}

    public function sellInData(Request $request){

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

}
