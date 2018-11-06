<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\DisplayShare;
use App\DetailDisplayShare;
use JWTAuth;
use Config;
use Carbon\Carbon;

class DisplayShareController extends Controller
{
    public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

    public function store(Request $request){

        $content = json_decode($request->getContent(),true);
        $employee = JWTAuth::parseToken()->authenticate();


        // Check Display Share header
        $displayShareHeader = DisplayShare::where('id_employee', $employee->id)->where('id_store', $content['id'])->where('date', date('Y-m-d'))->first();

            if ($displayShareHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $displayShareHeader, $employee) {


                        foreach ($content['data'] as $data) { 
                            DetailDisplayShare::create([
                                'id_display_share' => $displayShareHeader->id,
                                'id_category' => $content['id_category'],
                                'id_brand' => $content['id_brand'],
                                'tier' => $content['tier'],
                                'depht' => $content['depht'],
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                return response()->json(['status' => true, 'id_transaksi' => $displayShareHeader->id, 'message' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $employee) {

                        // HEADER
                        $transaction = DisplayShare::create([
                                            'id_store' => $content['id_store'],
                                            'id_employee' => $employee->id,
                                            'date' => Carbon::now(),
                                            'week' => $content['week'],
                                        ]);


                        foreach ($content['data'] as $data) { 
                            DetailDisplayShare::create([
                                'id_display_share' => $transaction->id,
                                'id_category' => $content['id_category'],
                                'id_brand' => $content['id_brand'],
                                'tier' => $content['tier'],
                                'depht' => $content['depht'],
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['status' => false, 'message' => 'Gagal melakukan transaksi'], 500);
                }

                // Check sell in(Sell Thru) header after insert
                $displayShareHeaderAfter = DisplayShare::where('id_employee', $employee->id)->where('id_store', $content['id'])->where('date', date('Y-m-d'))->first();

                return response()->json(['status' => true, 'id_transaksi' => $displayShareHeaderAfter->id, 'message' => 'Data berhasil di input']);

            }

    }

}
