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
use DB;
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
                                'other_currency'    => $row->r ?? null,
                                'other_description' => $row->s ?? null,
                                'total_cost'        => $row->t ?? null,
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
}
