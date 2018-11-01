<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\Brand;
use App\Employee;
use App\Category;
use App\Product;

class BrandController extends Controller
{
    public function baca()
    {
        return view('product.brand');
    }

    public function store(Request $request)
    {
        Brand::create([
            'name'       => $request->input('name'),
            'keterangan'       => $request->input('keterangan'),
        ]);
        return redirect()->back()
        ->with([
            'type'   => 'success',
            'title'  => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah brand!'
        ]);
    }

    public function data()
    {
        $brand = Brand::get();
        return Datatables::of($brand)
        ->addColumn('action', function ($brand) {
            if($brand->product->isEmpty()){
                return '<button onclick="editModal('.$brand->id.',&#39;'.$brand->name.'&#39;,&#39;'.$brand->keterangan.'&#39;)" class="btn btn-sm btn-primary btn-square"><i class="si si-pencil"></i></button>
                <button data-url='.route("brand.delete", $brand->id).' class="btn btn-sm btn-danger btn-square js-swal-delete"><i class="si si-trash"></i></button>';
            }
        })->make(true);
    }

    public function update(Request $request, $id) 
	{
		$brand = Brand::find($id);
			$brand->name = $request->get('name');
			$brand->keterangan = $request->get('keterangan');
			$brand->save();
			return redirect()->back()
			->with([
				'type'    => 'success',
				'title'   => 'Sukses!<br/>',
				'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah brand!'
			]);
	}

    public function delete($id)
    {
        $brand = Brand::find($id);
            $cat = Product::where(['id_brand' => $brand->id])->count();
            // $cat = Employee::where(['id_brand' => $brand->id])->count();
            if (!$cat < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain di Category Product, Product Summary dan Price atau di Employee Pages!'
                ]);
            } else {
                $brand->delete();
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
                ]);
            }
    }
}
