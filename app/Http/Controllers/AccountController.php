<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use DB;
use Auth;
use File;
use Excel;
use App\Account;
use App\Channel;
use Yajra\Datatables\Datatables;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Filters\AccountFilters;

class AccountController extends Controller
{

    public function getDataWithFilters(AccountFilters $filters){
        $data = Account::filter($filters)->get();
        return $data;
    }

    public function baca()
    {
        $data['channel'] = Channel::get();
        return view('store.account', $data);
    }

    public function getDataWithFilters(AccountFilters $filters){
        $data = Account::filter($filters)->get();
        return $data;
    }

    public function data(Request $request)
    {
        $account = Account::with('channel')
        ->select('accounts.*');
        return Datatables::of($account)
        ->addColumn('action', function ($account) {
            $data = array(
                'id'            => $account->id,
                'channel'       => $account->channel->id,
                'name'          => $account->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('account.delete', $account->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->escapeColumns([])->make(true);
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            'channel'        => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = Channel::where(['id' => $request->input('channel')])->first();
            $check = Account::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")
            ->where(['id_channel' => $data->id])->count();
            if ($check < 1) {
                Account::create([
                    'name'          => $request->input('name'),
                    'id_channel'    => $request->input('channel'),
                ]);
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah account!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Account atau Channel sudah ada!'
                ]);
            }
        }   
    }

    public function update(Request $request, $id) 
	{
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            'channel'        => 'required|numeric',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $data = Channel::where(['id' => $request->get('channel')])->first();
            $check = Account::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->get('name'))."'")
            ->where(['id_channel' => $data->id])->count();
            if ($check < 1) {
		        $account = Account::find($id);
                    $account->name          = $request->get('name');
                    $account->id_channel    = $request->get('channel');
		        	$account->save();
		        	return redirect()->back()
		        	->with([
		        		'type'    => 'success',
		        		'title'   => 'Sukses!<br/>',
		        		'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah account!'
		        	]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Account atau Channel sudah ada!'
                ]);
            }
        }
    }

    //Github fast Excel
    // public function import(Request $request)
    // {
        //     $reader=$request->all();
        //         $limit = [
        //             'file' => 'required|mimeTypes:'.
        //             'application/vnd.ms-office,'.
        //             'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'.
        //             'application/vnd.ms-excel',     
        //         ];
        //         $validator = Validator($reader, $limit);
        //         if ($validator->fails()){
        //             return redirect()->back()
        //             ->withErrors($validator)
        //             ->withInput();
        //         } else {
        //         $path = $request->file('file')->store('excel-files/account');
        //         $import = (new FastExcel)->import(storage_path('app/' . $path), function ($reader) {
        //             if (!empty($reader['channel']) && !empty($reader['account'])) {
        //                 $data =  Channel::where([
        //                     'name' => $reader['channel']
        //                     ])->first();
        //                 // dd($data);
        //                 if (!$data)
        //                 {
        //                     $insert = Channel::create([
        //                         'name'          => $reader['channel'],
        //                     ]);
        //                     if ($insert->id) {
        //                         Account::create([
        //                             'name'          => $reader['account'],
        //                             'id_channel' 	=> $insert->id
        //                         ]);
        //                         return true;
        //                     } else {
        //                         return false;
        //                     }
        //                 } else {
        //                     Account::create([
        //                         'name'          => $reader['account'],
        //                         'id_channel'    => $data->id,
        //                     ]);
        //                     return true;
        //                 }
        //             } else {
        //                 return redirect()->back()
        //                 ->withErrors( 'Gagal Mengimpor data dari Excel, dikarenakan datanya tidak sesuai!. <');
        //             }
        //         });
        //     }
        //     if ($import) {
        //         return redirect()->back()
        //         ->with([
        //             'type'      => 'success',
        //             'title'     => 'Sukses!<br/>',
        //             'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengimport data account dari Excel!'
        //         ]);
        //     } else { 
        //         return redirect()->back()
        //         ->with([
        //             'type'      => 'danger',
        //             'title'     => 'Gagal!<br/>',
        //             'message'   => '<i class="em em-confetti_ball mr-2"></i>Gagal mengimport data account dari Excel!'
        //         ]);
        //     }
    // }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' =>   'required|mimeTypes:'.
                        'application/vnd.ms-office,'.
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'.
                        'application/vnd.ms-excel'
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
                
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results)
                    {
                        foreach($results as $row)
                        {
                            echo "$row<hr>";
                            // CEK ISSET CUSTOMER
                            $dataAccount['channel_name']   = $row->channel;
                            $id_channel = $this->findChannel($dataAccount);

                            Account::create([
                                'id_channel'    => $id_channel,
                                'name'          => $row->account,
                            ]);
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

    public function findChannel($data)
    {
        $dataAccount = Channel::where('name','like','%'.trim($data['channel_name']).'%');
        if ($dataAccount->count() == 0) {
            
            $channel = Channel::create([
              'name'        => $data['channel_name'],
            ]);
            $id_channel = $channel->id;
        }else{
            $id_channel = $dataAccount->first()->id;
        }
      return $id_channel;
    }

    public function delete($id) 
    {
        $account = Account::find($id);
            $account->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
}
