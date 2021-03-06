<?php

namespace App\Http\Controllers;

use App\Area;
use App\Region;
use App\SubCategory;
use App\Category;
use App\ProductStockType;
use App\Channel;
use App\FokusChannel;
use App\FokusProduct;
use App\FokusArea;
use App\ProductFokus;
use App\Product;
use Exception;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;

class ProductFokusController extends Controller
{
    private $alert = [
        'type' => 'success',
        'title' => 'Sukses!<br/>',
        'message' => 'Berhasil melakukan aski.'
    ];

    public function baca()
    {
        $data['area']   = Area::get();
        return view('product.fokus', $data);
    }

    public function data()
    {
        $fokus = FokusProduct::get();
        $data = array();
        $chan = array();
        $areas = array();
        $id = 1;
        foreach ($fokus as $key => $value) {
            $channel = FokusChannel::where('id_pf', $value->id_pf)->get();
            $area = FokusArea::where('id_pf', $value->id_pf)->get();
            foreach ($area as $ar) {
                $areas[$key][] = (isset($ar->area->name) ? $ar->area->name : "-");
            }
            foreach ($channel as $val) {
                $chan[$key][] = $val->channel->name;
            }
            $data[] = array(
                'id' => $id++,
                'id_pf' => $value->id_pf,
                'product' => $value->product->name,
                'channel' => implode(',', $chan[$key]),
                'area' => (isset($areas[$key]) ? implode(',', $areas[$key]) : "-"),
                'from' => $value->pf->from,
                'until' => $value->pf->to,
            );
        }
        return Datatables::of(collect($data))
        ->addColumn('action', function ($fokus) {
            $product = ProductFokus::where('id', $fokus['id_pf'])->first();
            $data = array(
                'id'            => $fokus['id'],
                'product'       => FokusProduct::where('id_pf',$product->id)->pluck('id_product'),
                'area'          => FokusArea::where('id_pf',$product->id)->pluck('id_area'),
                'from'          => $product->from,
                'to'          	=> $product->to,
                'channel'       => FokusChannel::where('id_pf',$product->id)->pluck('id_channel')
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('fokus.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (($validator = ProductFokus::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $from = explode('/', $data['from']);
        $data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
        $to = explode('/', $data['to']);
        $data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

        if (ProductFokus::hasActivePF($data)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus sudah ada!';
        } else {
            DB::transaction(function () use($data) {
                $productData = $data['product'];
                unset($data['product']);
                $channel = $data['channel'];
                unset($data['channel']);
                $area = (isset($data['area']) ? $data['area'] : null);
                unset($data['area']);
                $check = ProductFokus::where('from', $data['from'])
                ->where('to', $data['to'])->first();
                if (!$check) {
                    $product = ProductFokus::create($data);
                    foreach ($channel as $channel_id) {
                        FokusChannel::create([
                            'id_pf'              => $product->id,
                            'id_channel'         => $channel_id
                        ]);
                    }
                    foreach ($productData as $product_id) {
                        FokusProduct::create([
                            'id_pf'              => $product->id,
                            'id_product'         => $product_id
                        ]);
                    }
                    if (!empty($area)) {
                        foreach ($area as $area_id) {
                            FokusArea::create([
                                'id_pf'              => $product->id,
                                'id_area'            => $area_id
                            ]);
                        }
                    }
                } else {
                    foreach ($channel as $channel_id) {
                        FokusChannel::create([
                            'id_pf'              => $check->id,
                            'id_channel'         => $channel_id
                        ]);
                    }
                    foreach ($productData as $product_id) {
                        FokusProduct::create([
                            'id_pf'              => $check->id,
                            'id_product'         => $product_id
                        ]);
                    }
                    if (!empty($area)) {
                        foreach ($area as $area_id) {
                            FokusArea::create([
                                'id_pf'              => $check->id,
                                'id_area'            => $area_id
                            ]);
                        }
                    }
                }
            });
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus!';
        }

        return redirect()->back()->with($this->alert);
    }

    public function update(Request $request, $id)
    {
        $data=$request->all();
        $limit=[
            'from'          => 'required',
            'to'            => 'required',
            'product'       => 'required',
            'channel'       => 'required'
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
            $fokus = ProductFokus::find($id);
                if ($request->input('product')) {
                    foreach ($request->input('product') as $product) {
                        FokusProduct::where('id_pf', $id)->delete();
                        $dataProduct[] = array(
                            'id_product'        => $product,
                            'id_pf'             => $id,
                        );
                    }
                    DB::table('fokus_products')->insert($dataProduct);
                }
                if ($request->input('channel')) {
                    foreach ($request->input('channel') as $channel) {
                        FokusChannel::where('id_pf', $id)->delete();
                        $dataChannel[] = array(
                            'id_channel'        => $channel,
                            'id_pf'             => $id,
                        );
                    }
                    DB::table('fokus_channels')->insert($dataChannel);
                }
                if (!empty($request->input('area'))) {
                    foreach ($request->input('area') as $area) {
                        FokusChannel::where('id_pf', $id)->delete();
                        $dataArea[] = array(
                            'id_pf'         => $fokus->id,
                            'id_area'       => $area
                        );
                    }
                    DB::table('fokus_areas')->insert($dataArea);
                }
              
        
                // if ($request->input('to')) {
                //     $to = Carbon::createFromFormat('d/m/Y','28/'.$request->input('to'))->endOfMonth()->format('d/m/Y');
                // } else {
                //     $to = null;
                // }
                // if ($request->input('from')) {
                //     $from = Carbon::createFromFormat('d/m/Y','01/'.$request->input('from'))->endOfMonth()->format('m/Y');
                // } else {
                //     $from = null;
                // }
                $from = explode('/', $data['from']); 
                $to = explode('/', $data['to']);
                $data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
                $data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();
                $fokus->fill($data)->save();
                return redirect()->back()
                ->with([
                    'type'    => 'success',
                    'title'   => 'Sukses!<br/>',
                    'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah Product Fokus!'
                ]);
        }
    }
 
   

    public function delete($id)
    {
        $product = ProductFokus::find($id);
        $product->delete();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
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
                                $fokus = ProductFokus::create([
                                    'from'  => \PHPExcel_Style_NumberFormat::toFormattedString($row['from'], 'YYYY-MM'),
                                    'to'    => \PHPExcel_Style_NumberFormat::toFormattedString($row['until'], 'YYYY-MM')
                                ]);
                                if (!isset($fokus->id)) {

                                } else {
                                    $listProduct = explode(",", $row->sku);
                                    // dd($listProduct);
                                    foreach ($listProduct as $key => $product) {
                                        $getSku = Product::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($product))."'")->first();
                                        if (isset($getSku->id)) {
                                            FokusProduct::create([
                                                'id_pf'         => $fokus->id,
                                                'id_product'    => $getSku->id
                                            ]);
                                        }
                                    }
                                    $listChannel = explode(",", $row->channel);
                                    foreach ($listChannel as $channel) {
                                        FokusChannel::create([
                                            'id_channel'    => $this->findChannel($channel),
                                            'id_pf'         => $fokus->id
                                        ]);
                                    }
                                    $listArea = explode(",", $row->area);
                                    foreach ($listArea as $area) {
                                        $getArea = \App\Area::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($area))."'")->first();
                                        if (isset($getArea->id)) {
                                            FokusArea::create([
                                                'id_area'            => $getArea->id,
                                                'id_pf'              => $fokus->id
                                            ]);
                                        }
                                    }
                                    // dd($listArea);
                                    // dd($getArea);
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
                            'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah produk target!'
                        ]);
                    }
                }, false);
                return redirect()->back()->with([
                    'type' => 'success',
                    'title' => 'Sukses!<br/>',
                    'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk target!'
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

    public function findChannel($channel)
    {
        $dataChannel = Channel::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($channel))."'");
        if ($dataChannel->count() == 0) {
            $data2 = Channel::create([
                'name'  => $channel
            ]);
            if ($data2) {
                $id_channel = $data2->id;
            }
        } else {
            $id_channel = $dataChannel->first()->id;
        }
        return $id_channel;
    }

    public function export()
    {
        $emp = ProductFokus::orderBy('created_at', 'DESC');
        if ($emp->count() > 0) {
            foreach ($emp->get() as $val) {
                $area = FokusArea::where(
                    'id_pf', $val->id
                )->get();
                $areaList = array();
                foreach($area as $dataArea) {
                    if(isset($dataArea->id_area)) {
                        $areaList[] = $dataArea->area->name;
                    } else {
                        $areaList[] = "-";
                    }
                }
                $channel = FokusChannel::where(
                    'id_pf', $val->id
                )->get();
                $channelList = array();
                foreach($channel as $dataChannel) {
                    if(isset($dataChannel->id_channel)) {
                        $channelList[] = $dataChannel->channel->name;
                    } else {
                        $channelList[] = "-";
                    }
                }
                $product = FokusProduct::where(
                    'id_pf', $val->id
                )->get();
                $productList = array();
                foreach($product as $dataProduct) {
                    if(isset($dataProduct->id_product)) {
                        $productList[] = $dataProduct->product->name;
                    } else {
                        $productList[] = "-";
                    }
                }
                $data[] = array(
                    'Product'		=> rtrim(implode(',', $productList), ','),
                    'Channel'	    => rtrim(implode(',', $channelList), ','),
                    'Area'			=> rtrim(implode(',', $areaList), ','),
                    'Month From'    => (isset($val->from) ? $val->from : "-"),
                    'Month Until'   => (isset($val->to) ? $val->to : "-")
                );
            }

            $filename = "ProductFokus_".Carbon::now().".xlsx";
            return Excel::create($filename, function($excel) use ($data) {
                $excel->sheet('Employee', function($sheet) use ($data)
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

}
