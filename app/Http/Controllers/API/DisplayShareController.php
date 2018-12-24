<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Components\traits\WeekHelper;
use App\DisplayShare;
use App\DetailDisplayShare;
use JWTAuth;
use Config;
use Carbon\Carbon;

class DisplayShareController extends Controller
{
    use WeekHelper;

    public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

    public function store(Request $request){

        $content = json_decode($request->getContent(),true);
        $employee = JWTAuth::parseToken()->authenticate();
        // return response()->json($content);

        // Check Display Share header
        $displayShareHeader = DisplayShare::where('id_employee', $employee->id)->where('id_store', $content['store'])->where('date', date('Y-m-d'))->first();

            if ($displayShareHeader) { // If header exist (update and/or create detail)

                try {
                    DB::transaction(function () use ($content, $displayShareHeader, $employee) {


                        foreach ($content['data'] as $data) { 
                            DetailDisplayShare::create([
                                'id_display_share' => $displayShareHeader->id,
                                'id_category' => $data['id_category'],
                                'id_brand' => $data['id_brand'],
                                'tier' => $data['tier'],
                                'depth' => $data['depth'],
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'msg' => 'Gagal melakukan transaksi 1'], 500);
                }

                return response()->json(['success' => true, 'id_transaksi' => $displayShareHeader->id, 'msg' => 'Data berhasil di input']);

            } else { // If header didn't exist (create header & detail)

                try {
                    DB::transaction(function () use ($content, $employee) {

                        // HEADER
                        $transaction = DisplayShare::create([
                                            'id_store' => $content['store'],
                                            'id_employee' => $employee->id,
                                            'date' => Carbon::now(),
                                            'week' => $this->getWeek(Carbon::now()->toDateString()),
                                        ]);


                        foreach ($content['data'] as $data) { 
                            DetailDisplayShare::create([
                                'id_display_share' => $transaction->id,
                                'id_category' => $data['id_category'],
                                'id_brand' => $data['id_brand'],
                                'tier' => $data['tier'],
                                'depth' => $data['depth'],
                            ]);

                        }

                    });
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'msg' => 'Gagal melakukan transaksi 2'], 500);
                }

                // Check sell in(Sell Thru) header after insert
                $displayShareHeaderAfter = DisplayShare::where('id_employee', $employee->id)->where('id_store', $content['store'])->where('date', date('Y-m-d'))->first();

                return response()->json(['success' => true, 'id_transaksi' => $displayShareHeaderAfter->id, 'msg' => 'Data berhasil di input']);

            }

    }

}
