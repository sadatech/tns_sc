<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Distributor;
use Auth;
use App\StoreDistributor;
use Rap2hpoutre\FastExcel\FastExcel;

class DistributorController extends Controller
{
    public function baca()
    {
        return view('store.distributor');
    }

    public function store(Request $request)
    {
        $data=$request->all();
        $limit=[
            'name'        => 'required',
            'code'        => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            Distributor::create([
               'name'       => $request->input('name'),
               'code'       => $request->input('code'),
            ]);
            return redirect()->back()
            ->with([
                'type'    => 'success',
                'title'   => 'Sukses!<br/>',
                'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah distributor!'
            ]);
        }
    }

    public function data()
    {
        $distributor = Distributor::get();
        return Datatables::of($distributor)
        ->addColumn('action', function ($distributor) {
            $data = array(
                'id'            => $distributor->id,
                'code'          => $distributor->code,
                'name'          => $distributor->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('distributor.delete', $distributor->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })->escapeColumns([])->make(true);
    }

    public function update(Request $request, $id) 
	{
		$distributor = Distributor::find($id);
            $distributor->name = $request->get('name');
            $distributor->code = $request->get('code');
			$distributor->save();
			return redirect()->back()
			->with([
				'type'    => 'success',
				'title'   => 'Sukses!<br/>',
				'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah area!'
			]);
    }
    
    public function import(Request $request)
    {
        $reader=$request->all();
            $limit = [
                'file' => 'required|mimeTypes:'.
                'application/vnd.ms-office,'.
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,'.
                'application/vnd.ms-excel',     
            ];
            $validator = Validator($reader, $limit);
            if ($validator->fails()){
                return redirect()->back()
                ->withErrors($validator)
                ->withInput();
            } else {
            $path = $request->file('file')->store('excel-files/distributor');
            $import = (new FastExcel)->import(storage_path('app/' . $path), function ($reader) {
                if (!empty($reader['code']) && !empty($reader['distributor'])) 
                {
                    // $data = Distributor::where([
                    //     'code'          => $reader['code']
                    // ])->first();
                // if($data == $reader['code'])
                // {
                Distributor::create([
                    'code'        => $reader['code'],
                    'name'        => $reader['distributor'],
                ]);
                // } else {
                //     return redirect()->back()
                //     ->withErrors('Gagal Mengimport Data, dikarenanakan code ada yang sama<');
                // }
                } else {
                    return redirect()->back()
                    ->withErrors('Gagal Mengimport Data, dikarenakan data tidak sesuai dengan Sample Data<');
                }
            });
        }
        if ($import) {
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengimport data distributor dari Excel!'
            ]);
        } else { 
            return redirect()->back()
            ->with([
                'type'      => 'danger',
                'title'     => 'Gagal!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Gagal mengimport data distributor dari Excel!'
            ]);
        }

    }

    public function delete($id) 
    {
        $distributor = Distributor::find($id);
            $duct = StoreDistributor::where(['id_distributor' => $distributor->id])->count();
            if (!$duct < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lainnya di Store!'
                ]);
            } else {
                $distributor->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            }
    }
}
