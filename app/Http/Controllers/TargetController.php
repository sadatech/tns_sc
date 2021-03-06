<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Employee;
use App\Store;
use App\Target;
use Auth;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use App\MtcReportTemplate;
use Exception;

class TargetController extends Controller
{
    public function baca()
    {
    	$data['employee'] = Employee::get();
        $data['store']    = Store::get();
        return view('product.target',$data);
    }

    public function data()
    {
        $product = Target::with(['store','employee', 'product'])->select('targets.*');
        return Datatables::of($product)
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
                'employee'     	=> $product->employee->id,
                'store'         => $product->store->name1,
                'quantity'      => $product->quantity,
                'rilis'         => $product->rilis
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('mtc.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {        
        try {
            $data = $request->all();
            if (($validator = Target::validate($data))->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $file = Input::file('file')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($extension != 'xlsx' && $extension !=  'xls') {
                return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
            }

            if($request->hasFile('file')) {
                $file = $request->file('file')->getRealPath();
                $ext = '';
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use($request) {
                    try {
                        if (!empty($results->all())) {
                            $mY = explode('/', $request->rilis);
                            foreach($results as $row) {
                                $target = Target::updateOrCreate([
                                    // 'rilis' => Carbon::parse("01/".$request->rilis),
                                    'rilis' => Carbon::create($mY[1], $mY[0], 1),
                                    'id_employee' => $request->id_employee,
                                    'id_store' => $row->id_store,
                                    'id_product' => \App\Product::where('name', $row->product_name)->first()->id,
                                ], [
                                    'quantity' => $row->quantity,
                                ]);
                                if (!isset($target->id)) {
                                    throw new Exception("Error Processing Request", 1);
                                }

                                $date   = Carbon::parse($target->rilis);
                                $reportTemplate = MtcReportTemplate::where([
                                    'id_employee'   => $target->id_employee,
                                    'id_store'      => $target->id_store,
                                    'id_product'    => $target->id_product
                                ])
                                ->whereYear('date',$date->year)
                                ->whereMonth('date',$date->month)
                                ->get();
                                if ($reportTemplate->count() <= 0) {
                                    MtcReportTemplate::create([
                                        'id_employee'   => $target->id_employee,
                                        'id_store'      => $target->id_store,
                                        'id_product'    => $target->id_product,
                                        'date'          => Carbon::create($mY[1], $mY[0], Carbon::now()->daysInMonth),
                                    ]);
                                }
                            }
                            DB::commit();
                        } else {
                            throw new Exception("Error Processing Request", 1);
                            
                        }
                    } catch (Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with([
                            'type' => 'danger',
                            'title' => 'Gagal!<br/>',
                            'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah produk target!'
                        ]);
                    }
                }, false);
                return redirect()->back()->with([
                    'type' => 'success',
                    'title' => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk target!'
                ]);
            } else {
                DB::rollback();
                return redirect()->back()->with([
                    'type' => 'danger',
                    'title' => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>File harus di isi!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with([
                'type' => 'danger',
                'title' => 'Gagal!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah produk target!'
            ]);
        }
    }

    public function downloadSampleForm($employee_id)
    {
        Excel::create('TargetExcelFormat', function($excel) use($employee_id) {
            $excel->sheet('Target Format', function($sheet){
                $sheet->cells('A1:C1', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->row(1, ['ID STORE', 'PRODUCT NAME', 'QUANTITY']);
            });
            $excel->sheet('Store List', function($sheet) use ($employee_id) {
                $sheet->cells('A1:D1', function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                });
                $sheet->row(1, ['ID STORE', 'NAME 1', 'NAME 2', 'ADDRESS']);
                foreach (\App\EmployeeStore::where('id_employee', $employee_id)->with('store')->get() as $es) {
                    $sheet->appendRow([
                        @$es->store->id, 
                        @$es->store->name1, 
                        @$es->store->name2,
                        @$es->store->address
                    ]);
                }
            });
        })->export('xlsx');
        return ;
    }

    public function update(Request $request, $id) 
    {
        DB::transaction(function () use ($data, $user, &$res) {
            $product = Target::findOrFail($id);
            $product->fill($request->all());
            $product->save();

            // $date   = Carbon::parse($request->rilis);
            // $reportTemplate = MtcReportTemplate::where([
            //     'id_employee'   => $request->id_employee,
            //     'id_store'      => $request->id_store,
            //     'id_product'    => $request->id_product
            // ])
            // ->whereYear('date',$date->year)
            // ->whereMonth('date',$date->month)
            // ->get();
            // if ($reportTemplate->count() <= 0) {
            //     MtcReportTemplate::create([
            //         'id_employee'   => $request->id_employee,
            //         'id_store'      => $request->id_store,
            //         'id_product'    => $request->id_product,
            //         'date'          => $date
            //     ]);
            // }

        });
        return redirect()->back()->with([
          'type'    => 'success',
          'title'   => 'Sukses!<br/>',
          'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Target MTC!'
      ]);
    }

    public function delete($id)
    {
        $product = Target::find($id);
        $product->delete();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
    }

    public function exportXLS()
    {
        $x = Target::with(['store','employee', 'product'])->select('targets.*');
        if ($x->count() > 0) {
            foreach ($x->get() as $val) {
                $data[] = array(
                    'employee'      => $val->employee->id,
                    'store'         => $val->store->name1,
                    'quantity'      => $val->quantity,
                    'rilis'         => $val->rilis
                );
            }
        
            $filename = "TargetMtc_".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('TargetMtc', function($sheet) use ($data)
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
}
