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

class SubCategoryController extends Controller
{
    public function baca()
    {
        $data['category'] = Category::get();
        return view('product.subcategory', $data);
    }

    public function data()
    {
        $subcategory = SubCategory::get();
        return Datatables::of($subcategory)
        ->addColumn('category', function($subcategory) {
			if (isset($subcategory->category)) {
				$category = $subcategory->category->name;
			} else {
				$category = "Without category";
			}
            return $category;
        })
        ->addColumn('action', function ($subcategory) {
            if (isset($subcategory->category)) {
				$category = $subcategory->category->id;
			} else {
				$category = "Without category";
			}
            $data = array(
                'id'        => $subcategory->id,
                'category'     => $category,
                'name'      => $subcategory->name
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('sub-category.delete', $subcategory->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
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
            SubCategory::create([
                'name'          => $request->input('name'),
                'id_category'      => $request->input('category'),
            ]);
            return redirect()->back()
            ->with([
                'type' => 'success',
                'title' => 'Sukses!<br/>',
                'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah SubCategory Product!'
            ]);
        }
    }

    public function update(Request $request, $id) 
    {
      $subcategory = SubCategory::find($id);
        $subcategory->name         = $request->get('name');
        $subcategory->id_category    = $request->get('category');
        $subcategory->save();
        return redirect()->back()
        ->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah subcategory product!'
        ]);
    }

    public function delete($id) 
    {
        $subcategory = SubCategory::find($id);
            $duct = Product::where(['id_category' => $subcategory->id])->count();
            if (!$duct < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lainnya di Product dan Price!'
                ]);
            } else {
                $subcategory->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            }
    }
}
