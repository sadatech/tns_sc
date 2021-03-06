<?php

namespace App\Http\Controllers\Target;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use App\TargetGtc;
use App\Employee;
use Carbon\Carbon;
use Exception;
use DB;
use Auth;
use File;
use Excel;

class SmdController extends Controller
{
    public function baca()
    {
        $data['employee']      = Employee::where(['isResign' => 0])
        ->where('id_position', 4)
        ->get();
        return view('target.smd', $data);
    }

    public function data()
    {
        $target = TargetGtc::with('employee')
        ->select('target_gtcs.*');
        return Datatables::of($target)
        ->editColumn('rilis', function($target){
            $rilis = Carbon::parse($target->rilis)->format('F Y');
            return $rilis;
        })
        ->addColumn('values', function($target){
            return number_format($target->value_sales,2,',','.');
        })
        ->addColumn('action', function ($target) {
            $data = array(
                'id'            => $target->id,
                'employee'      => $target->employee->id,
                'value'         => $target->value_sales,
                'hk'            => $target->hk,
                'ec'            => $target->ec,
                'rilis'         => $target->rilis,
                'cbd'           => $target->cbd
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('target.smd.delete', $target->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'employee'      => 'required|numeric',
            'ec'            => 'required|numeric',
            'hk'            => 'required|numeric',
            'cbd'           => 'required',
            'value'         => 'required',
            'rilis'         => 'required|date'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            TargetGtc::create([
                'id_employee'   => $request->input('employee'),
                'rilis'         => $request->input('rilis'),
                'hk'            => $request->input('hk'),
                'ec'            => $request->input('ec'),
                'cbd'           => $request->input('cbd'),
                'value_sales'   => $request->input('value')

            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk target!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'employee'      => 'required|numeric',
            'ec'            => 'required|numeric',
            'hk'            => 'required|numeric',
            'cbd'           => 'required|numeric',
            'value'         => 'required',
            'rilis'         => 'required|date'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $target = TargetGtc::find($id);
            $target->id_employee    = $request->get('employee');
            $target->rilis          = $request->get('rilis');
            $target->value_sales    = $request->get('value');
            $target->hk             = $request->get('hk');
            $target->ec             = $request->get('ec');
            $target->cbd            = $request->get('cbd');
            if ($target->save()) {
                return redirect()->back()->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah target!'
                ]);
            } else {
                return redirect()->route('employee')
                ->with([
                    'type'      => 'danger',
                    'title'     => 'Terjadi Kesalahan!<br/>',
                    'message'   => '<i class="em em-thinking_face mr-2"></i>Gagal mengupdate data!'
                ]);
            }
        }
    }

    public function delete($id)
    {
        $product = TargetGtc::find($id);
        if ($product->delete()) {
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
        } else {
            return redirect()->back()
            ->with([
                'type'      => 'danger',
                'title'     => 'Terjadi Kesalahan!<br/>',
                'message'   => '<i class="em em-thinking_face mr-2"></i>Gagal menghapus data!'
            ]);
        }
    }

    public function export()
	{
        $x = TargetGtc::orderBy('created_at', 'DESC');
        if ($x->count() > 0) {
		    foreach ($x->get() as $val) {
		    	$data[] = array(
		    		'Employee'		=> $val->employee->name,
                    'HK'	        => $val->hk,
                    'ReleaseDate'	=> $val->rilis,
                    'SalesValue'    => $val->value_sales,
                    'EC'            => $val->ec,
                    'CBD'           => $val->cbd
		    	);
            }
        
		    $filename = "TargetGtc_".Carbon::now().".xlsx";
		    return Excel::create($filename, function($excel) use ($data) {
		    	$excel->sheet('TargetGtc', function($sheet) use ($data)
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

    public function importXLS(Request $request)
    {
        try {
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
                        DB::beginTransaction();
                        if (!empty($results->all())) {
                            foreach($results as $row)
                            {
                                if ($row['employee'] != null) {

                                    $rowRules = [
                                        'employee'  => 'required',
                                        'hk'		=> 'required|numeric',
                                        'value'		=> 'required|numeric',
                                        'ecpf'      => 'required|numeric',
                                        'cbd'	    => 'required|numeric'
                                    ];
                                    $validator = Validator($row->toArray(), $rowRules);
                                    if ($validator->fails()) {
                                        return redirect()->back()
                                        ->withErrors($validator)
                                        ->withInput();
                                    } else {
                                         TargetGtc::create([
                                            'id_employee'   => \App\Employee::where('name', $row['employee'])->first()->id,
                                            'hk'            => $row['hk'],
                                            'rilis'         => Carbon::parse($row['rilis']),
                                            'value_sales'   => $row['value'],
                                            'ec'            => $row['ecpf'],
                                            'cbd'           => $row['cbd']

                                        ]);
                                    }
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
                            'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah Target SMD Pasar!'
                        ]);
                    }
                }, false);
                return redirect()->back()->with([
                    'type' => 'success',
                    'title' => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Target SMD Pasar!'
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
}
