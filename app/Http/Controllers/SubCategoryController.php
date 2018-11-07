<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use App\Product;
use App\Category;
use App\SubCategory;

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
                'name'             => $request->input('name'),
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
            $duct = Product::where(['id_subcategory' => $subcategory->id])->count();
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

    public function import(Request $request)
    {

        $this->validate($request, [
            'file' => 'required'
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
                            $dataCategory['name']       = $row->category;
                            $dataCategory['deskripsi']  = $row->deskripsi;
                            $id_category = $this->findCategory($dataCategory);

                            $data1 = Category::where([ 'id' => $id_category])->first();
                            $check = SubCategory::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($row->subcategory))."'")
                            ->where(['id_category' => $data1->id])->count();
                            if ($check < 1) {
                                SubCategory::create([
                                    'id_category'       => $id_category,
                                    'name'              => $row->subcategory
                                ])->id;
                            } else {
                                return false;
                            }
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
        } else {
            return redirect()->back()
            ->with([
                'type'    => 'danger',
                'title'   => 'Gagal!<br/>',
                'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
            ]);
        }
    }

    public function findCategory($data)
    {
        $dataCat = Category::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['name']))."'");
        if ($dataCat->count() < 1 ) {
            $category = Category::create([
              'name'              => $data['name'],
              'description'       => (isset($data['deskripsi']) ? $data['deskripsi'] : "-")
          ]);
            $id_category = $category->id;
        }else{
            $id_category = $dataCat->first()->id;
        }
        return $id_category;
    }

    public function export()
    {
        $dataSub = SubCategory::orderBy('created_at', 'DESC')->get();
        $data = array();
        foreach ($dataSub as $val) {
            $data[] = array(
                'SubCategory'       => $val->name,
                'Category'          => (isset($val->category->name) ? $val->category->name : "-"),
                'Deskripsi'         => (isset($val->category->description) ? $val->category->description : "-")
            );
        }
        $filename = "SubCategory_".Carbon::now().".xlsx";
        return Excel::create($filename, function($excel) use ($data) {
            $excel->sheet('SubCategory', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download();
    }
}
