<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Employee;
use App\Store;
use App\Target;
use Auth;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;

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
        $data = $request->all();

        if (($validator = Target::validate($data))->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($request) {
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
                    foreach($results as $row) {
                        Target::updateOrCreate([
                            'rilis' => $request->rilis,
                            'id_employee' => $request->id_employee,
                            'id_store' => $row->id_store,
                            'id_product' => \App\Product::where('name', $row->product_name)->first()->id,
                        ], [
                            'quantity' => $row->quantity,
                        ]);
                    }
                }, false);
            }
        });


        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Target MTC!'
        ]);
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
                        $es->store->id, 
                        $es->store->name1, 
                        $es->store->name2,
                        $es->store->address
                    ]);
                }
            });
        })->export('xlsx');
        return ;
    }

    public function update(Request $request, $id) 
    {
        $product = Target::findOrFail($id);
        $product->fill($request->all());
        $product->save();
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
}
