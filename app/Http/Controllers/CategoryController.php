<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
use App\Product;
use App\Category;
use App\SubCategory;
use App\Brand;

class CategoryController extends Controller
{
    public function baca()
    {
        return view('product.category');
    }

    public function data()
    {
        $category = Category::get();
        return Datatables::of($category)
   //      ->addColumn('brand', function($category) {
			// if (isset($category->brand)) {
			// 	$brand = $category->brand->name;
			// } else {
			// 	$brand = "Without Brand";
			// }
   //          return $brand;
   //      })
        ->addColumn('action', function ($category) {
   //          if (isset($category->brand)) {
			// 	$brand = $category->brand->id;
			// } else {
			// 	$brand = "Without Brand";
			// }
            $data = array(
                'id'        => $category->id,
                'name'      => $category->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('category.delete', $category->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
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
            Category::create([
                'name'          => $request->input('name'),
                // 'id_brand'      => $request->input('brand'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Category Product!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
      $category = Category::find($id);
        $category->name         = $request->get('name');
        // $category->id_brand    = $request->get('brand');
        $category->save();
        return redirect()->back()
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah category product!'
        ]);
    }

    public function delete($id) 
    {
        $category = Category::find($id);
            $duct = SubCategory::where(['id_category' => $category->id])->count();
            if (!$duct < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lainnya di Product dan Price!'
                ]);
            } else {
                $category->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            }
    }
}
