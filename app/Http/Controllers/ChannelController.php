<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\Channel;
use App\Account;

class ChannelController extends Controller
{
    public function baca()
    {
        return view('store.channel');
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = Channel::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")->count();
            if ($check < 1) {
                Channel::create([
                    'name'       => $request->input('name'),
                ]);
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah channel!'
                ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Channel sudah ada!'
                ]);
            }
        }
    }

    public function data()
    {
        $channel = Channel::get();
        return Datatables::of($channel)
        ->addColumn('name', function($channel) {
            return $channel->name;
        })
        ->addColumn('action', function ($channel) {
            return '<button onclick="editModal('.$channel->id.',&#39;'.$channel->name.'&#39;)" class="btn btn-sm btn-primary btn-square"><i class="si si-pencil"></i></button>
            <button data-url='.route("channel.delete", $channel->id).' class="btn btn-sm btn-danger btn-square js-swal-delete"><i class="si si-trash"></i></button>';
        })->make(true);
    }

    public function update(Request $request, $id) 
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = Channel::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->get('name'))."'")->count();
            if ($check < 1) {
                $channel = Channel::find($id);
                    $channel->name = $request->get('name');
                    $channel->save();
                    return redirect()->back()
                    ->with([
                     'type'    => 'success',
                     'title'   => 'Sukses!<br/>',
                     'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah chanel!'
                    ]);
            } else {
                return redirect()->back()
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Channel sudah ada!'
                ]);
            }
        }
    }

    public function delete($id) 
    {
        $channel = Channel::find($id);
            $account = Account::where([
                'id_channel' => $channel->id
            ])->count();
            if (!$account < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain (account dan store)!'
                ]);
            } else {
                $channel->delete();
                return redirect()->back()
                ->with([
                    'type'   => 'success',
                    'title'  => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
                ]);
            }
    }
}