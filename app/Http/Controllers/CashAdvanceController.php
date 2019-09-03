<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use File;
use Excel;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\CashAdvance;
use App\Employee;
use App\Area;
use DB;

use App\JobTrace;
use App\Jobs\ExportDCReportCashAdvanceJob;

class CashAdvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('report.democooking.cash');
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
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use ($request)
                {
                    foreach($results as $key => $row)
                    {
                        if ($key > 5 and $row->a != '') {
                            $period = explode('-', $request->periode);
                            $period = $period[0].'-'.$period[1];
                            $create = CashAdvance::create([
                                'id_employee'       => $request->id_employee,
                                'id_area'           => $request->id_area,
                                'date'              => !empty($row->a) ? $period.'-'.$row->a: null,
                                'description'       => $row->b ?? null,
                                'km_begin'          => $row->c ?? null,
                                'km_end'            => $row->d ?? null,
                                'km_distance'       => $row->e ?? null,
                                'tpd'               => $row->f ?? null,
                                'hotel'             => $row->g ?? null,
                                'bbm'               => $row->h ?? null,
                                'parking_and_toll'  => $row->i ?? null,
                                'raw_material'      => $row->j ?? null,
                                'property'          => $row->k ?? null,
                                'permission'        => $row->l ?? null,
                                'bus'               => $row->m ?? null,
                                'sipa'              => $row->n ?? null,
                                'taxibike'          => $row->o ?? null,
                                'rickshaw'          => $row->p ?? null,
                                'taxi'              => $row->q ?? null,
                                'other_cost'        => $row->r ?? null,
                                'other_description' => $row->s ?? null,
                                'total_cost'        => $row->t ?? null,
                                'price_profit'      => $row->u ?? null,
                                'subsidi_sasa'      => $row->v ?? null,
                            ]);
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

    public function data(Request $req)
    {
        $CashAdvance = CashAdvance::where("date",'>=', Carbon::parse($req->periodeFrom)->format("Y-m-d"))
        ->where("date",'<=', Carbon::parse($req->periodeTo)->format("Y-m-d"));
        if ($req->id_tl) {
            $CashAdvance->where("id_employee", $req->id_tl);
        }
        if ($req->id_area) {
            $CashAdvance->where("id_area", $req->id_area);
        }
        $CashAdvance->get();

        return Datatables::of($CashAdvance)
        ->addColumn("tgl", function($item){
            return Carbon::parse($item->date)->format("d");
        })
        ->addColumn("employee", function($item){
            return $item->employee->name;
        })
        ->addColumn("trasnport", function($item){
            $tranport = $item->bus+$item->sipa+$item->taxibike+$item->rickshaw+$item->taxi;
            return $tranport;
        })
        ->make(true);
    }

    public function exportXLS($id_area, $filterPeriodeFrom, $filterPeriodeTo, $filterTl)
    {
        $result = DB::transaction(function() use ($id_area, $filterPeriodeFrom, $filterPeriodeTo, $filterTl){
            try
            {
                if ($id_area != null && $id_area != 'null' && $filterTl != null && $filterTl != 'null') {
                    $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                    $JobTrace = JobTrace::create([
                        'id_user' => Auth::user()->id,
                        'date' => Carbon::now(),
                        'title' => "Demo Cooking - Report Cash Advance - " . Area::where("id", $id_area)->first()->name. " - "  . Employee::where("id", $filterTl)->first()->name. " - " . Carbon::parse($filterPeriodeFrom)->format("d M Y") ." to : " . Carbon::parse($filterPeriodeTo)->format("d M Y") . " (" . $filecode . ")",
                        'status' => 'PROCESSING',
                    ]);
                }elseif ($id_area != null && $id_area != 'null') {
                    $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                    $JobTrace = JobTrace::create([
                        'id_user' => Auth::user()->id,
                        'date' => Carbon::now(),
                        'title' => "Demo Cooking - Report Cash Advance - " . Area::where("id", $id_area)->first()->name. " - " . Carbon::parse($filterPeriodeFrom)->format("d M Y") ." to : " . Carbon::parse($filterPeriodeTo)->format("d M Y") . " (" . $filecode . ")",
                        'status' => 'PROCESSING',
                    ]);
                }elseif ($filterTl != null && $filterTl != 'null') {
                    $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                    $JobTrace = JobTrace::create([
                        'id_user' => Auth::user()->id,
                        'date' => Carbon::now(),
                        'title' => "Demo Cooking - Report Cash Advance - " . Employee::where("id", $filterTl)->first()->name. " - " . Carbon::parse($filterPeriodeFrom)->format("d M Y") ." to : " . Carbon::parse($filterPeriodeTo)->format("d M Y") . " (" . $filecode . ")",
                        'status' => 'PROCESSING',
                    ]);
                }else{
                    $filecode = "@".substr(str_replace("-", null, crc32(md5(time()))), 0, 9);
                    $JobTrace = JobTrace::create([
                        'id_user' => Auth::user()->id,
                        'date' => Carbon::now(),
                        'title' => "Demo Cooking - Report Cash Advance - " . Carbon::parse($filterPeriodeFrom)->format("d M Y") ." to : " . Carbon::parse($filterPeriodeTo)->format("d M Y") . " (" . $filecode . ")",
                        'status' => 'PROCESSING',
                    ]);
                }
                dispatch(new ExportDCReportCashAdvanceJob($JobTrace, $id_area, $filterPeriodeFrom, $filterPeriodeTo, $filterTl, $filecode));
                return 'Export succeed, please go to download page';
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return 'Export request failed '.$e->getMessage();
            }
        });
        return response()->json(["result"=>$result], 200, [], JSON_PRETTY_PRINT);
    }

}
