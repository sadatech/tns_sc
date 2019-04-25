<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\SalesDc;
use App\SalesDcDetail;
use App\Product;
use Yajra\Datatables\Datatables;

class ReportEditDcController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dcSales(Request $request)
    {
        $request['area'] = ($request->area == "null" || empty($request->area) ? null : $request->area);
        $sales = SalesDc::orderBy('sales_dcs.created_at', 'DESC')->join('sales_dc_details','sales_dcs.id','sales_dc_details.id_sales')->where('sales_dc_details.deleted_at' ,null)
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
        ->when($request->has('date'), function ($q) use ($request){
            return $q->whereDay('date', substr($request->input('date'), 8))
            ->whereMonth('date', substr($request->input('date'), 0, 2))
            ->whereYear('date', substr($request->input('date'), 3, 4));
        })
        ->when($request->has('area'), function($q) use ($request)
        {
            return $q->join('employees','sales_dcs.id_employee','employees.id')
                        ->join('employee_sub_areas','employees.id','employee_sub_areas.id_employee')
                        ->join('sub_areas','employee_sub_areas.id_subarea','sub_areas.id')
                        ->where('sub_areas.id_area', $request->input('area'));
        })
        ->get();

        $data = array();
        $id = 1;
        // return response()->json($sales->get());
        foreach ($sales as $value) {
            if($value->employee->position->level == 'dc'){
                $data[$id] = array(
                    'id'            => $id++,
                    // 'id_sales'      => $value->id,
                    'id_detail'     => $value->id,
                    'nama'          => (isset($value->employee->name) ? $value->employee->name : ""),
                    'place'         => (isset($value->place) ? $value->place : ""),
                    'icip_icip'     => $value->icip_icip ?? "",
                    'channel'       => $value->channel ?? "",
                    'effective_contact' => $value->effective_contact ?? "",
                    'tanggal'       => (isset($value->date) ? $value->date : ""),
                    'id_product'    => $value->id_product,
                    // 'qty'           => $value->qty,
                    'qty_actual'    => $value->qty_actual,
                    'satuan'        => $value->satuan,

                );
            }
            $product = Product::where('id',$data[$id]['id_product'])->first();
            $data[$id] = array_merge($data[$id], ['product' => $product->name]);
        }
        $dt = Datatables::of(collect($data));
        $dt->addColumn('action', function ($sales) {
            return "<button onclick='editModal(".json_encode($sales).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('edit.gtc.dc.sales.delete', $sales['id_detail'])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
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
    public function updateDcSales(Request $request, $id)
    {
        $salesDetail = SalesDcDetail::find($id);

        $salesDetail->update([
            'id_product'    => $request->product,
            'qty'           => $request->qty_actual,
            'qty_actual'    => $request->qty_actual,
            'satuan'        => $request->satuan,
            ]);

        $sales = SalesDc::where('id',$salesDetail->id_sales)->first();


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
    public function deleteDcSales($id)
    {
        $salesDetail = SalesDcDetail::find($id);
        $salesDetail->delete();

        $seles = SalesDc::where('id',$salesDetail->id_sales)->first();
        $salesOn = SalesDcDetail::where('id_sales',$seles->id)->first();
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


}
