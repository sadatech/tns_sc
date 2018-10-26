<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JobTrace;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use Carbon\Carbon;
use App\Jobs\ExportJob;


class UtilitiesController extends Controller
{
    //
    public function reportDownloadIndex(){
    	return view('utilities.report_download');
    }

    public function reportDownloadData(){
    	$data = JobTrace::where('id_company', Auth::user()->id_company);

        return Datatables::of($data)
        ->addColumn('action', function ($item) {
            $data = array(
                'id'            => $item->id,
                'qty'           => $item->quantity
            );

            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('sellin.delete', $item->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>
            ";
        })->make(true);
    }

    public function reportDownloadExport(Request $request){

    	// JOB TRACING AND QUEUE
    	$trace = JobTrace::create([
                'id_company' => 1,
                'id_user' => 1,
                'date' => Carbon::now(),
                'title' => $request->title.' '.Carbon::now()->format('d-m-Y H:i:s'),
                'status' => 'PROCESSING',            
            ]);

    	dispatch(new ExportJob($trace, $request->all(), Auth::user()));
    	return $request->all();
    }

    public function reportDownloadExportAll(Request $request){
    	return $request->all();
    }
}
