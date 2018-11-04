<?php

namespace App\Http\Controllers;

use App\SkuUnit;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SkuUnitController extends Controller
{
    public function baca()
    {
        return view('product.sku_unit');
    }

    public function data()
    {
        $skuUnit = SkuUnit::get();
        return Datatables::of($skuUnit)
        ->addColumn('action', function ($row) {
            $data = $row->toArray();
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('sub-category.delete', $row->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $skuUnit = new SkuUnit;
        if (($validator = $skuUnit->validate($request->all()))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $skuUnit->fill($request->all())->save();
        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah SKU Unit!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $skuUnit = SkuUnit::findOrFail($id);

        if (($validator = $skuUnit->validate($request->all()))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $skuUnit->fill($request->all())->save();
        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah SKU Unit!'
        ]);
    }

    public function destroy($id)
    {
        SkuUnit::findOrFail($id)->delete();

        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menghapus SKU Unit!'
        ]);
    }

    public function export()
    {
 
        $data = SkuUnit::orderBy('created_at', 'DESC')->get();
        $filename = "SkuUnit".Carbon::now().".xlsx";
        (new FastExcel($data))->download($filename, function ($data) {
            return [
                'name'                => $data->name,
                'value'               => $data->conversion_value,
            ];
        });
    }

    public function import(Request $request)
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
                        $check = SkuUnit::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->name))."'")
                        ->where(['conversion_value' => $row->value])->count();
                        // dd($check);
                        if ($check < 1) {
                            SkuUnit::create([
                                'name'              => $row->name,
                                'conversion_value'  => $row->value
                            ])->id;
                        } else {
                            return false;
                        }
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
}
