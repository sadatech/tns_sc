<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\AdditionalDisplay;
use App\DetailAdditionalDisplay;
use JWTAuth;
use Config;
use Carbon\Carbon;

class AdditionalDisplayController extends Controller
{
    public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

    public function store(Request $request){

        $content = json_decode($request->getContent(),true);
        $employee = JWTAuth::parseToken()->authenticate();


        // Check Display Share header
        $additionalDisplayHeader = AdditionalDisplay::where('id_employee', $employee->id)->where('id_store', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($additionalDisplayHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $additionalDisplayHeader, $employee) {

                        if ($req['foto_additional'] != null)
                        {
                            $file_name = $additionalDisplayHeader->id . '-' .$content['foto_additional']->getClientOriginalExtension();
                            $content['foto_additional']->move(
                                public_path('images/report/AdditionalDisplay'),
                                $content['foto_additional'] = $file_name
                            );
                        }
                        DetailAdditionalDisplay::create([
                            'id_additional_display' => $additionalDisplayHeader->id,
                            'id_jenis_display' => $content['id_jenis_display'],
                            'jumlah' => '1',
                            'foto_additional' => $content['foto_additional'],
                        ]);

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                return response()->json(['status' => true, 'id_transaksi' => $additionalDisplayHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $employee) {

                        // HEADER
                        $transaction = AdditionalDisplay::create([
                                            'id_store' => $content['id_store'],
                                            'id_employee' => $employee->id,
                                            'date' => Carbon::now()
                                        ]);

                        DetailAdditionalDisplay::create([
                            'id_additional_display' => $transaction->id,
                            'id_jenis_display' => $content['id_jenis_display'],
                            'jumlah' => '1',
                            'foto_additional' => $content['foto_additional'],
                        ]);

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in(Sell Thru) header after insert
                $additionalDisplayHeaderAfter = AdditionalDisplay::where('id_employee', $employee->id)->where('id_store', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $additionalDisplayHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

    }

}
