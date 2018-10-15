<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\Store;
use App\Classification;

class ClassificationController extends Controller
{
    public function baca()
    {
        return view('store.classification');
    }

    public function store(Request $request)
    {
        $check = Classification::whereRaw("UPPER(name) LIKE '%". strtoupper($request->input('name'))."%'")->count();
        if ($check < 1) {
            Classification::create([
                'name'       => $request->input('name'),
            ]);
            return redirect()->back()
            ->with([
                'type'   => 'success',
                'title'  => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Classification!'
            ]);
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Classification sudah ada!'
            ]);
        }
    }

    public function data()
    {
        $classification = Classification::get();
        return Datatables::of($classification)
        ->addColumn('action', function ($classification) {
            $data = array(
                'id'            => $classification->id,
                'name'          => $classification->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('classification.delete', $classification->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->escapeColumns([])->make(true);
    }
    
    public function update(Request $request, $id) 
	{
        $check = Classification::whereRaw("UPPER(name) LIKE '%". strtoupper($request->input('name'))."%'")->count();
        if ($check < 1) {
		$classification = Classification::find($id);
		    	$classification->name = $request->get('name');
		    	$classification->save();
		    	return redirect()->back()
		    	->with([
		    		'type'    => 'success',
		    		'title'   => 'Sukses!<br/>',
		    		'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah classification!'
		    	]);
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Classification sudah ada!'
            ]);
        }
	}

    public function delete($id) 
    {
        $classification = Classification::find($id);
            $duct = Store::where(['id_classification' => $classification->id])->count();
            if (!$duct < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lainnya di Store!'
                ]);
            } else {
                $classification->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            }
    }
}
