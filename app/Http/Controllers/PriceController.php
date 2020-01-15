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
use App\ProductPrice;
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
        $price = ProductPrice::with('product')->orderBy('updated_at','desc');
        return Datatables::of($price)
        ->editColumn('retailer_price', function($price) {
            return ($price->retailer_price? $this->numberToPrice('Rp', $price->retailer_price) : '');
        })
        ->editColumn('consumer_price', function($price) {
            return ($price->consumer_price? $this->numberToPrice('Rp', $price->consumer_price) : '');
        })
        ->editColumn('release', function($price) {
            $release = explode('-', $price->release);
            return $release[2].'/'.$release[1].'/'.$release[0];
        })
        ->addColumn('product', function($price) {
            return ($price->product->name??'DELETED PRODUCT');
        })
        ->addColumn('category', function($price) {
            return ($price->product->subCategory->name??'DELETED PRODUCT');
        })
        ->addColumn('action', function ($price) {
            $release = explode('-', $price->release);
            $data = array(
                'id'                => $price->id,
                'product'           => $price->product,
                'release'           => $release[2].'/'.$release[1].'/'.$release[0],
                'retailer_price'    => $this->numberToPrice('', $price->retailer_price, '.'),
                'consumer_price'    => $this->numberToPrice('', $price->consumer_price, '.'),
            );
            return "<button data-toggle='modal' data-target='#formModal' onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('price.delete', $price->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->only('id_product','release','retailer_price','consumer_price');
        $data['retailer_price'] = str_replace('.', '', $data['retailer_price']);
        $data['consumer_price'] = str_replace('.', '', $data['consumer_price']);
        $data['release']        = Carbon::parse($request->release)->format('Y-m-d');

        if (($validator = ProductPrice::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $action = DB::transaction(function () use ($data, $request) {
                if($request->update == 1){
                    ProductPrice::whereId($request->id)->update($data);
                }
                if(empty($request->update)){
                    ProductPrice::withTrashed()->updateOrCreate($data,['deleted_at',null]);
                }

            return 'true';
        });

        if ($action == 'true') {
            $this->alert['type']    = 'success';
            $this->alert['title']   = 'Berhasil!<br/>';
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil '.($request->update == 1 ? 'mengubah' : 'menambah').' price!';
        } else {
            $this->alert['type']    = 'warning';
            $this->alert['title']   = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Gagal '.($request->update == 1 ? 'mengubah' : 'menambah').' price!';
        }
        return redirect()->back()->with($this->alert);
    }

    public function delete($id)
    {
        $price = ProductPrice::find($id);
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
        $price = ProductPrice::orderBy('updated_at', 'DESC');
        if ($price->count() > 0) {
            $data = array();
            foreach ($price->get() as $val) {
                $data[] = array(
                    'Product'           => $val->product->name??'DELETED PRODUCT',
                    'Sub Category'      => (isset($val->product->subCategory->name) ? $val->product->subCategory->name : "-"),
                    'Retailer Price'    => (isset($val->retailer_price) ? (integer)$val->retailer_price : "-"),
                    'Consumer Price'    => (isset($val->consumer_price) ? (integer)$val->consumer_price : "-"),
                    'Release Date'      => $val->release
                );
            }
            $filename = "Price_Product_".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('Price_Product', function($sheet) use ($data)
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
                $index = 1;
                $success = 'true';
                Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use($request, &$index, &$success) {
                    try {
                        DB::beginTransaction();
                        if (!empty($results->all())) {
                            foreach($results as $row)
                            {
                                $index++;
                                $product = Product::whereRaw("TRIM(UPPER(code)) = '".trim(strtoupper($row['product_code']))."'")->first();
                                if (!empty($product->id)) {

                                    $product = $product->id;

                                    $rowRules = [
                                        'product_code'      => 'required',
                                        'retailer_price'    => 'required|numeric',  
                                        'consumer_price'	=> 'required|numeric',	
                                        'release'		    => 'required'
                                    ];
                                    $validator = Validator($row->toArray(), $rowRules);
                                    if ($validator->fails()) {
                                        continue;
                                    } else {
                                         ProductPrice::updateOrCreate([
                                            'id_product'        => $product,
                                            'retailer_price'    => $row['retailer_price'],
                                            'consumer_price'    => $row['consumer_price'],
                                            'release'           => Carbon::parse($row['release'])->format('Y-m-d'),
                                        ]);
                                    }
                                }else{
                                    $index = $index.", Product \"$row[product_code]\" tidak ditemukan!";
                                    $success = 'false';
                                    break;
                                }
                            }
                            DB::commit();
                        } else {
                            throw new \Exception("Error Processing Request", 1);
                        }
                    } catch (Exception $e) {
                        DB::rollback();
                        return redirect()->back()->with([
                            'type'      => 'danger',
                            'title'     => 'Gagal!<br/>',
                            'message'   => '<i class="em em-confounded mr-2"></i>Gagal menambah Price Product!'.'<br>Cek baris '.$index
                        ]);
                    }
                }, false);

                if ($success != 'true') {
                    return redirect()->back()->with([
                        'type'      => 'danger',
                        'title'     => 'Gagal!<br/>',
                        'message'   => '<i class="em em-confounded mr-2"></i>Gagal menambah Price Product!'.'<br>Cek baris '.$index
                    ]);
                }else{
                    return redirect()->back()->with([
                        'type'      => 'success',
                        'title'     => 'Sukses!<br/>',
                        'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Price Product!'
                    ]);
                }
            } else {
                DB::rollback();
                return redirect()->back()->with([
                    'type'      => 'danger',
                    'title'     => 'Gagal!<br/>',
                    'message'   => '<i class="em em-confounded mr-2"></i>File harus di isi!'
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with([
                'type'      => 'danger',
                'title'     => 'Gagal!<br/>',
                'message'   => '<i class="em em-confounded mr-2"></i>Gagal import Price Produk!'
            ]);
        }
    }


}
