<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use App\Price;
use App\Product;
use App\Traits\StringTrait;

class PriceController extends Controller
{
    use StringTrait;

    public function baca()
    {
        $data['product'] = Product::get();
        return view('product.price', $data);
    }

    public function data()
    {
        $price = Price::with('product');
        return Datatables::of($price)
        ->editColumn('price', function($price) {
            return ($price->price? $this->numberToPrice('Rp', $price->price) : '');
        })

        ->editColumn('price_cs', function($price) {
            return ($price->price_cs? $this->numberToPrice('Rp', $price->price_cs) : '');
        })
        ->addColumn('product', function($price) {
            return ($price->product->name??'DELETED PRODUCT');
        })
        ->addColumn('category', function($price) {
            return ($price->product->subCategory->name??'DELETED PRODUCT');
        })
        ->addColumn('action', function ($price) {
            $data = array(
                'id'                => $price->id,
                'product'           => $price->product,
                'rilis'             => $price->rilis,
                'price'             => $price->price,
                'price_cs'          => $price->price_cs,
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('price.delete', $price->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if (($validator = Price::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if (Price::hasActivePF($data)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Price sudah ada!';
        } else {
            DB::transaction(function () use($data) {
                $product = Price::create($data);
            });
            $this->alert['type'] = 'success';
            $this->alert['title'] = 'Berhasil!<br/>';
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah price!';
        }
        return redirect()->back()->with($this->alert);
    }

    public function update(Request $request, $id) 
    {
        $product = Price::findOrFail($id);
        $data = $request->all();
        if (($validator = Price::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if (Price::hasActivePF($data, $product->id)) {
                $this->alert['type'] = 'warning';
                $this->alert['title'] = 'Warning!<br/>';
                $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Price sudah ada!';
            } else {
            DB::transaction(function () use($product, $data) {

                $product->fill($data)->save();
            });
            $this->alert['type'] = 'success';
            $this->alert['title'] = 'Berhasil!<br/>';
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah produk fokus!';
        }
        return redirect()->back()->with($this->alert);
    }

    public function delete($id)
    {
        $price = Price::find($id);
            $price->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
    
    public function exportXLS()
    {
        $price = Price::orderBy('created_at', 'DESC');
        if ($price->count() > 0) {
            $data = array();
            foreach ($price->get() as $val) {
                $data[] = array(
                    'Product'       => $val->product->name??'DELETED PRODUCT',
                    'SubCategory'   => (isset($val->product->subCategory->name) ? $val->product->subCategory->name : "-"),
                    'Price'         => (isset($val->price) ? $val->price : "-"),
                    'Price Cs'         => (isset($val->price_cs) ? $val->price : "-"),
                    'Rilis'         => (isset($val->rilis) ? $val->rilis : "-")
                );
            }
            $filename = "PriceProduct_".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('PriceProduct', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);
                });
            })->download();
        } else {
            return redirect()->back()
            ->with([
                'type'   => 'danger',
                'title'  => 'Gagal Unduh!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
            ]);
        }
    }

    public function importXLS(Request $request)
    {
        try {
            $file = Input::file('file')->getClientOriginalName();
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($extension != 'xlsx' && $extension !=  'xls') {
                return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
            }

            if($request->hasFile('file')) {
                $file = $request->file('file')->getRealPath();
                $ext = '';
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use($request) {
                    try {
                        DB::beginTransaction();
                        if (!empty($results->all())) {
                            foreach($results as $row)
                            {
                                $rowRules = [
                                    'product'  => 'required',
                                    'price'		=> 'required|numeric',	
                                    'rilis'		=> 'required'
                                ];
                                $validator = Validator($row->toArray(), $rowRules);
                                if ($validator->fails()) {
                                    continue;
                                } else {
                                     Price::create([
                                        'id_product'    => \App\Product::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($row['product']))."'")->withTrashed()->first()->id,
                                        'price'         => $row['price'],
                                        'price_cs'         => $row['price_cs'],
                                        'rilis'         => \PHPExcel_Style_NumberFormat::toFormattedString($row['rilis'], 'YYYY-MM-DD'),

                                    ]);
                                }
                            }
                            DB::commit();
                        } else {
                            throw new Exception("Error Processing Request", 1);
                        }
                    } catch (Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with([
                            'type' => 'danger',
                            'title' => 'Gagal!<br/>',
                            'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah Target Product!'
                        ]);
                    }
                }, false);
                return redirect()->back()->with([
                    'type' => 'success',
                    'title' => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Price Product!'
                ]);
            } else {
                DB::rollback();
                return redirect()->back()->with([
                    'type' => 'danger',
                    'title' => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>File harus di isi!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with([
                'type' => 'danger',
                'title' => 'Gagal!<br/>',
                'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah produk target!'
            ]);
        }
    }


}
