<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Alert;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
// use App\Helper\ExcelHelper as ExcelHelper;
use Illuminate\Database\Eloquent\Collection;
use Auth;
use File;
use App\ImportQueue;
use Yajra\Datatables\Datatables;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportQueueController extends Controller
{
	public function ImportSellIn(Request $request)
	{	
        $data = $request->all();

        $limit=[
			'file' 			=> 	'required|mimeTypes:xlxs,xlx'.
		                        'application/vnd.ms-office,'.
		                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'.
		                        'application/vnd.ms-excel'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
			$files = $data['file'];
			// $file = $files->getClientOriginalExtension();
            $filename = $files->getClientOriginalName();
			$file_path = 'import/sellin';
			$files->move($file_path, $filename);

            ImportQueue::create([
                'id_employee'   => Auth::user()->id,
                'date'			=> Carbon::now(),
                'file'			=> $filename,
                'type'			=> 'sellIn',
                'status'		=> 'onProses'
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil Upload Sell In!'
            ]);
        }
	}
	// public function sellInImportExcel()
	// {
	// 	if(Input::hasFile('import_file')){
	// 		$path = Input::file('import_file')->getRealPath();
	// 		$data = Excel::load($path, function($reader) {
	// 		})->get();
	// 		if(!empty($data) && $data->count()){
	// 			foreach ($data as $key => $value) {
 //            	$sellInHeader = SellIn::where('id_employee', $value->id_employee)->where('id_store', $value->id_store)->where('date', date('Y-m-d'))->first();
	// 				if ($sellInHeader) {
	// 					$detail = new DetailIn();
	// 					$detail->perkara_id = $sellInHeader->id;
	// 					$detail->id_product = $value->id_product;
	// 					$detail->price = $value->price;
	// 					$detail->qty = $value->qty;
	// 					$detail->isPf = $value->isPf;
	// 					$detail->save();
	// 				}else{
	// 					$perkara = new SellIn();
	// 					$perkara->id_store = $value->id_store;
	// 					$perkara->id_employee = $value->id_employee;
	// 					$perkara->date = $value->date;
	// 					$perkara->week = $value->week;
	// 					$perkara->save();

	// 					$detail = new DetailIn();
	// 					$detail->perkara_id = $perkara->id;
	// 					$detail->id_product = $value->id_product;
	// 					$detail->price = $value->price;
	// 					$detail->qty = $value->qty;
	// 					$detail->isPf = $value->isPf;
	// 					$detail->save();

	// 				}
	// 			}
 //        			Alert::success("Insert Record successfully.");
	// 		}
	// 	}
	// 	return back();
	// }

}
