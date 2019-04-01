<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\SalesMd;
use App\SalesMdDetail;
use App\Product;
use App\NewCbd;
use Yajra\Datatables\Datatables;

class ReportEditSmdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function SMDsales(Request $request)
    {
        $sales = SalesMd::orderBy('sales_mds.created_at', 'DESC')->join('sales_md_details','sales_mds.id','sales_md_details.id_sales')->where('sales_md_details.deleted_at' ,null);
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
        if ($request->has('area')) {
            $sales->whereHas('outlet.employeePasar.pasar.subarea.area', function($q) use ($request){
                return $q->where('id_area', $request->input('area'));
            }); 
        }
        if($request->has('date')) {
            $sales->whereDay('date', substr($request->input('date'), 8))
            ->whereMonth('date', substr($request->input('date'), 0, 2))
            ->whereYear('date', substr($request->input('date'), 3, 4));
        }
        $data = array();
        $id = 1;
        // return response()->json($sales->get());
        foreach ($sales->get() as $value) {
            if($value->employee->position->level == 'mdgtc'){
                $data[$id] = array(
                    'id'            => $id++,
                    'id_detail'     => $value->id,
                    'id_outlet'     => $value->id_outlet,
                    'id_employee'   => $value->id_employee,
                    'id_product'    => $value->id_product,
                    'date'          => (isset($value->date) ? $value->date : ""),
                    'nama'          => (isset($value->employee->name) ? $value->employee->name : ""),
                    'pasar'         => (isset($value->outlet->employeePasar->pasar->name) ? $value->outlet->employeePasar->pasar->name : ""),
                    'tanggal'       => $value->date,
                    'outlet'        => (isset($value->outlet->name) ? $value->outlet->name : ""),
                    'qty'           => $value->qty_actual,
                    'satuan'        => $value->satuan,
                );
            }
            $product = Product::where('id',$data[$id]['id_product'])->first();
            $data[$id] = array_merge($data[$id], ['product' => $product->name]);
        }
        $dt = Datatables::of(collect($data));
        $dt->addColumn('action', function ($sales) {
            return "<button onclick='editModal(".json_encode($sales).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('edit.gtc.smd.sales.delete', $sales['id_detail'])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        });

        return $dt->make(true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSales(Request $request, $id)
    {
        $salesDetail = SalesMdDetail::find($id);

        $salesDetail->update([
            'id_product'    => $request->product,
            'qty_actual'    => $request->qty,
            'satuan'        => $request->satuan,
            ]);

        $sales = SalesMd::where('id',$salesDetail->id_sales)->first();


        $sales->update([
            'id_outlet'     => $request->outlate,
            ]);

        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil merubah Sales!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteSales($id)
    {
        $salesDetail = SalesMdDetail::find($id);
        $salesDetail->delete();

        $seles = SalesMd::where('id',$salesDetail->id_sales)->first();
        $salesOn = SalesMdDetail::where('id_sales',$seles->id)->first();
        // return response()->json($salesOn);

        if ($salesOn == null) {
            // return response()->json($salesOn);
            $seles->delete();
        }

        return redirect()->back()
        ->with([
            'type' => 'success',
            'title' => 'Sukses! <br>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menghapus Sales!'
        ]);
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
        ->when($request->has('date'), function ($q) use ($request){
            return $q->whereDay('date', substr($request->input('date'), 8))
            ->whereMonth('date', substr($request->input('date'), 0, 2))
            ->whereYear('date', substr($request->input('date'), 3, 4));
        })
        ->when($request->has('outlet'), function ($q) use ($request){
            $q->whereHas('outlet', function($q2) use ($request){
                return $q2->where('id_outlet', $request->input('outlet'));
            });
        })
        ->when($request->has('area'), function ($q) use ($request){
            $q->whereHas('outlet.employeePasar.pasar.subarea.area', function($q2) use ($request){
                return $q2->where('id_area', $request->input('area'));
            });
        })
        ->get();

        // return $cbd;

        $data = array();
        $id = 1;
        foreach ($cbd as $val) {
            if ($val->employee->position->level == 'mdgtc'){
                $data[] = array(
                    'id'            => $id++,
                    'outlet'        => $val->outlet->name,
                    'id_outlet'     => $val->id_outlet,
                    'id_cbd'        => $val->id,
                    'region'        => (isset($val->outlet->employeePasar->pasar->subarea->area->region->name) ? $val->outlet->employeePasar->pasar->subarea->area->region->name : ""),
                    'area'          => (isset($val->outlet->employeePasar->pasar->subarea->area->name) ? $val->outlet->employeePasar->pasar->subarea->area->name : ""),
                    'subarea'       => (isset($val->outlet->employeePasar->pasar->subarea->name) ? $val->outlet->employeePasar->pasar->subarea->name : ""),
                    'pasar'         => (isset($val->outlet->employeePasar->pasar->name) ? $val->outlet->employeePasar->pasar->name : ""),
                    'employee'      => $val->employee->name,
                    'date'          => $val->date,
                    'cbd_competitor'=> str_replace('"','',$val->cbd_competitor),
                    'cbd_position'  => str_replace('"','',$val->cbd_position),
                    'outlet_type'   => str_replace('"','',$val->outlet_type),
                    'total_hanger'  => $val->total_hanger,
                    'status'        => ($val->status == 1) ? 'Approve' : 'Reject',
                    'posm_shop_sign'       => $val->posm_shop_sign,
                    'posm_hangering_mobile'=> $val->posm_hangering_mobile,
                    'posm_poster'          => $val->posm_poster,
                    'posm_others'          => str_replace('"','',$val->posm_others)?? '-',
                    'posm_shop_sign_display'       => ($val->posm_shop_sign == 1) ? 'Yes' : 'No',
                    'posm_hangering_mobile_display'=> ($val->posm_hangering_mobile == 1) ? 'Yes' : 'No',
                    'posm_poster_display'          => ($val->posm_poster == 1) ? 'Yes' : 'No',
                );
            }
        }

        $dt = Datatables::of(collect($data));
        // return response()->json($data);
        $dt->addColumn('action', function ($cbd) {
            return "<button onclick='editModal(".json_encode($cbd).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('edit.gtc.smd.new-cbd.delete', $cbd['id_cbd'])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        });
        
        return $dt->make(true);
    }

    public function updateNewCbd(Request $request, $id)
    {
        // return response()->json($request);
        $salesDetail = NewCbd::find($id);

        $salesDetail->update([
            'id_outlet'             => $request->outlate,
            'total_hanger'          => $request->total_hanger,
            'cbd_position'          => $request->cbd_position,
            'outlet_type'           => $request->outlet_type,
            'cbd_competitor'        => $request->cbd_competitor,
            'posm_shop_sign'        => $request->posm_shop_sign,
            'posm_hangering_mobile' => $request->posm_hangering_mobile,
            'posm_poster'           => $request->posm_poster,
            'posm_others'           => $request->posm_others,
            ]);

        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil merubah Sales!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteNewCbd($id)
    {
        $salesDetail = NewCbd::find($id);
        // return response()->json($id);
        $salesDetail->delete();

        return redirect()->back()
        ->with([
            'type' => 'success',
            'title' => 'Sukses! <br>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menghapus Sales!'
        ]);
    }
}
