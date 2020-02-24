<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use DB;
use Auth;
use Yajra\Datatables\Datatables;
use App\JobTrace;
use App\Traits\UploadTrait;

class JobTraceController extends Controller
{

    use UploadTrait;

    public function data($model, $type ='')
    {
        $model = str_replace('bAckSlasH','\\',$model);

        $data = JobTrace::
            when($type == 'U', function($q){
                return $q->whereType('U');
            })
            ->when($type == 'D', function($q){
                return $q->whereType('D');
            })
            ->whereModel($model)
            ->orderBy('id', 'desc')
            ->get();

        return Datatables::of($data)
            ->editColumn('log', function ($item) {
                if($item->log == "" || $item->log == null){
                    return "";
                }
                return "<button class='btn btn-primary btn-sm errorLog'data-toggle='confirmation' data-singleton='true' value='".$item->log."'><i class='fa fa-commenting-o'></i></button>";
            })
            ->addColumn('request_by', function ($item) {
                return @$item->user->name;
            })
            ->addColumn('type', function ($item) {
                return @$item->type == 'U' ? 'Upload' : 'Download';
            })
            ->addColumn('file', function ($item) {
                return $this->checkFileExist($item->id) .' '. $item->title;
            })
            ->editColumn('status', function ($item) {
                return $this->convertTraceStatus($item->status, $item->id);
            })
            ->addColumn('action', function ($item) {
                return "<button class='btn btn-danger btn-sm btn-delete deleteButton' data-toggle='confirmation' data-singleton='true' value='".$item->id."'><i class='fa fa-remove'></i></button>";
            })
            ->rawColumns(['log', 'status', 'file', 'action'])
            ->make(true);
    }

    public function destroy($id = '')
    {
        if ($id != '') {
            JobTrace::destroy($id);

            return response()->json($id);
        }
    }

}
