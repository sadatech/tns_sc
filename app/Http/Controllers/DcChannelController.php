<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\DcChannel;

class DcChannelController extends Controller
{
    public function baca()
    {
        return view('store.dcchannel');
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'          => 'required',
            'code'          => 'required',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $check = DcChannel::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->input('name'))."'")->count();
            if ($check < 1) {
                DcChannel::create([
                    'name'       => $request->input('name'),
                    'code'       => $request->input('code'),
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
        $channel = DcChannel::get();
        return Datatables::of($channel)
        ->addColumn('action', function ($channel) {
            return '<button onclick="editModal('.$channel->id.',&#39;'.$channel->name.'&#39;)" class="btn btn-sm btn-primary btn-square"><i class="si si-pencil"></i></button>
            <button data-url='.route("dc_channel.delete", $channel->id).' class="btn btn-sm btn-danger btn-square js-swal-delete"><i class="si si-trash"></i></button>';
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
            $check = DcChannel::whereRaw("TRIM(UPPER(name)) = '". strtoupper($request->get('name'))."'")->count();
            if ($check < 1) {
                $channel = DcChannel::find($id);
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
        $channel = DcChannel::find($id);
        $channel->delete();
        return redirect()->back()
        ->with([
            'type'   => 'success',
            'title'  => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
    }
}