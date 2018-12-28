<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Components\traits\WeekHelper;
use App\AdditionalDisplay;
use App\DetailAdditionalDisplay;
use App\JenisDisplay;
use JWTAuth;
use Config;
use Carbon\Carbon;

class AdditionalDisplayController extends Controller
{
    use WeekHelper;

    public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

    public function store(Request $request){

        // $content = json_decode($request->getContent(),true);
        $employee = JWTAuth::parseToken()->authenticate();
        // return response()->json($content);


        // Check Display Share header
        $additionalDisplayHeader = AdditionalDisplay::where('id_employee', $employee->id)->where('id_store', $request['store'])->where('date', date('Y-m-d'))->first();

            if ($additionalDisplayHeader) { // If header exist (update and/or create detail)

                // try {
                    DB::transaction(function () use ($request, $additionalDisplayHeader, $employee) {

                        if ($request['foto_additional'] != null)
                        {
                            $file_name = $additionalDisplayHeader->id . '.' .$request['foto_additional']->getClientOriginalExtension();
                            $request['foto_additional']->move(
                                public_path('images/report/AdditionalDisplay'),
                                $req['foto_additional'] = $file_name
                            );
                        }
                        DetailAdditionalDisplay::create([
                            'id_additional_display' => $additionalDisplayHeader->id,
                            'id_jenis_display' => $request['jenis_display'],
                            'jumlah' => '1',
                            'foto_additional' => $req['foto_additional'],
                        ]);

                    });
                // } catch (\Exception $e) {
                //     return response()->json(['success' => false, 'msg' => 'Gagal melakukan transaksi 1'], 500);
                // }

                return response()->json(['success' => true, 'id_transaksi' => $additionalDisplayHeader->id, 'msg' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                // try {
                    DB::transaction(function () use ($request, $employee) {

                        // HEADER
                        $transaction = AdditionalDisplay::create([
                                            'id_store' => $request['store'],
                                            'id_employee' => $employee->id,
                                            'date' => Carbon::now(),
                                            'week' => $this->getWeek(Carbon::now()->toDateString()),
                                        ]);

                        if ($request['foto_additional'] != null)
                        {
                            $file_name = $transaction->id . '.' .$request['foto_additional']->getClientOriginalExtension();
                            $request['foto_additional']->move(
                                public_path('images/report/AdditionalDisplay'),
                                $req['foto_additional'] = $file_name
                            );
                        }
                        
                        DetailAdditionalDisplay::create([
                            'id_additional_display' => $transaction->id,
                            'id_jenis_display' => $request['jenis_display'],
                            'jumlah' => '1',
                            'foto_additional' => $req['foto_additional'],
                        ]);

                    });
                // } catch (\Exception $e) {
                //     return response()->json(['success' => false, 'msg' => 'Gagal melakukan transaksi 2'], 500);
                // }

                // Check sell in(Sell Thru) header after insert
                $additionalDisplayHeaderAfter = AdditionalDisplay::where('id_employee', $employee->id)->where('id_store', $request['store'])->where('date', date('Y-m-d'))->first();

                return response()->json(['success' => true, 'id_transaksi' => $additionalDisplayHeaderAfter->id, 'msg' => 'Data berhasil di input']);

            }

    }

    public function jenisDisplay(){
        $jenis_display = JenisDisplay::get();

        return response()->json($jenis_display, 200);
    }

}
