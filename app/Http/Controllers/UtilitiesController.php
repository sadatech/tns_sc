<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JobTrace;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use Carbon\Carbon;
use App\Filters\JobTraceFilters;

class UtilitiesController extends Controller
{
    //
    public function reportDownloadIndex(){
    	return view('utilities.export_trace');
    }

    public function reportDownloadData(JobTraceFilters $filters){
    	$data = JobTrace::filter($filters);

        return Datatables::of($data)
            ->addColumn('request_by', function ($item) {
                return @$item->user->name;
            })
            ->editColumn('status', function ($item) {
                switch ($item->status) {
                    case 'PROCESSING':
                        return "<label style='cursor: default;' class='btn btn-sm btn-primary'>PROCESSING</label>";
                        break;

                    case 'DONE':
                        return "<label style='cursor: default;' class='btn btn-sm btn-success'>DONE</label>";
                        break;

                    case 'FAILED':
                        return "<label style='cursor: default;' class='btn btn-sm btn-danger'>FAILED</label>";
                        break;
                }
            })
            ->addColumn('action', function ($item) {
                $mode = 0;
                $action = '';
                if(Auth::user()->role->level == 'MasterAdmin'){
                    $mode += 1;
                    $action .= "<button onclick='editModal(".json_encode(['type' => 'edit', 'id' => $item->id, 'text' => $item->explanation]).")' class='btn btn-sm btn-warning btn-square' title='Add Explanation'><i class='si si-pencil'></i></button>";
                }
                if($item->explanation != null || $item->explanation != ''){
                    $mode += 1;
                    $action .= " <button onclick='editModal(".json_encode(['type' => 'show', 'id' => $item->id, 'text' => $item->explanation]).")' class='btn btn-sm btn-primary btn-square' title='See Explanation'><i class='si si-speech'></i></button>";
                }
                if($item->status == 'DONE' && ($item->results != '' || $item->results != null)){
                    $mode += 1;
                    $action .= " <a target='_blank' href='".$item->results."' class='btn btn-sm btn-success btn-square' title='Download File'><i class='si si-cloud-download'></i></a>";
                }
                if($mode == 0){
                    return '-';
                }
                return $action;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function reportDownloadAddExplanation(Request $request, $id){
        
        $trace = JobTrace::where('id', $id)->first();

        if($trace) $trace->update($request->all());

        return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil update explanation!'
            ]);

    }

}
