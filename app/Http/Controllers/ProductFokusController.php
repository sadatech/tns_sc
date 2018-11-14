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
        $product = ProductFokus::with(['product','fokusarea','fokus'])
        ->select('product_fokuses.*');
        return Datatables::of($product)
        // ->addColumn('area', function($product) {
		// 	if (isset($product->area)) {
		// 		$area = $product->area->name;
		// 	} else {
		// 		$area = "Without Area";
		// 	}
		// 	return $area;
        // })
        ->addColumn('fokusproduct', function($product) {
            $sku = FokusProduct::where(['id_pf'=>$product->id])->get();
            $skuList = array();
            foreach ($sku as $data) {
                $skuList[] = $data->product->name;
            }
            return rtrim(implode(',', $skuList), ',');
        })
        ->addColumn('fokusarea', function($product) {
            $area = FokusArea::where(['id_pf'=>$product->id])->get();
            $areaList = array();
            foreach ($area as $data) {
                $areaList[] = (isset($data->area->name) ? $data->area->name : "-");
            }
            return rtrim(implode(',', $areaList), ',');
        })
        ->addColumn('fokus', function($product) {
            $chan = FokusChannel::where(['id_pf'=>$product->id])->get();
            $channelList = array();
            foreach ($chan as $data) {
                $channelList[] = $data->channel->name;
            }
            return rtrim(implode(',', $channelList), ',');
        })
        ->addColumn('action', function ($product) {
            $data = array(
                'id'            => $product->id,
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

        // if (ProductFokus::hasActivePF($data)) {
        //     $this->alert['type'] = 'warning';
        //     $this->alert['title'] = 'Warning!<br/>';
        //     $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus sudah ada!';
        // } else {
            DB::transaction(function () use($data) {
                $productData = $data['product'];
                unset($data['product']);
                $channel = $data['channel'];
                unset($data['channel']);
                $area = (isset($data['area']) ? $data['area'] : null);
                unset($data['area']);
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
            });
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus!';
        // }

        return redirect()->back()->with($this->alert);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->all();
        $product = ProductFokus::findOrFail($id);

        if (($validator = $product->validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $from = explode('/', $data['from']); 
        $to = explode('/', $data['to']);
        $data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
        $data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

        // if (ProductFokus::hasActivePF($data, $product->id)) {
        //     $this->alert['type'] = 'warning';
        //     $this->alert['title'] = 'Warning!<br/>';
        //     $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus sudah ada!';
        // } else {
            DB::transaction(function () use($product, $data) {
                $channel = $data['channel'];
                unset($data['channel']);
                $sku = $data['product'];
                unset($data['sku']);
                $area = (isset($data['area']) ? $data['area'] : null);
                unset($data['area']);
                $product->fill($data)->save();

                $oldChanel = $product->fokus->pluck('id_channel');
                $deleteChannel = $oldChanel->diff($channel);
                $oldProduct = $product->fokus->pluck('id_product');
                $deleteProduct = $oldProduct->diff($sku);
                foreach ($deleteProduct as $deleteProduct) {
                    FokusProduct::where([
                        'id_pf'         => $product->id,
                        'id_product'    => $deleteProduct])->delete(); 
                }

                foreach ($sku as $product_id) {
                    FokusProduct::updateOrCreate([
                        'id_pf'         => $product->id,
                        'id_product'    => $product_id
                    ]);
                }
                FokusArea::where([
                    'id_pf'         => $product->id])->delete(); 
                if (!empty($area)) {
                    foreach ($area as $area_id) {
                        FokusArea::updateOrCreate([
                            'id_pf'         => $product->id,
                            'id_area'       => $area_id
                        ]);
                    } 
                }
                foreach ($deleteChannel as $deleted_id) {
                    FokusChannel::where([
                        'id_pf'         => $product->id,
                        'id_channel'    => $deleted_id])->delete(); 
                }

                foreach ($channel as $channel_id) {
                    FokusChannel::updateOrCreate([
                        'id_pf'         => $product->id,
                        'id_channel'       => $channel_id
                    ]);
                }
            });
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus!';
        // }

        return redirect()->back()->with($this->alert);
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
                        if (!empty($results->all())) {
                            foreach($results as $row)
                            {
                                // $rowRules = [
                                //     'sku'   => 'required',
                                //     'channel'   => 'required'
                                // ];
                                // $validator = Validator($row->toArray(), $rowRules);
                                // if ($validator->fails()) {
                                //     return redirect()->back()
                                //     ->withErrors($validator)
                                //     ->withInput();
                                // } else {
                                    $fokus = ProductFokus::updateOrCreate([
                                        'from'  => Carbon::now(),
                                        'to'    => Carbon::now() 
                                    ]);
                                    if (!empty($fokus)) {
                                        $dataProduct = array();
                                        $listProduct = explode(",", $row->sku);
                                            foreach ($listProduct as $sku) {
                                                $dataProduct[] = array(
                                                    'id_product'    		=> \App\Product::where(['name' => $sku])->first()->id,
                                                    'id_pf'          	    => $fokus->id,
                                                );
                                            }
                                        DB::table('fokus_products')->insert($dataProduct);
                                        $dataChannel = array();
                                        $listChannel = explode(",", $row->channel);
                                            foreach ($listChannel as $channel) {
                                                    $dataChannel[] = array(
                                                    'id_channel'    	=> $this->findChannel($channel),
                                                    'id_pf'          	=> $fokus->id
                                                );
                                            }
                                            DB::table('fokus_channels')->insert($dataChannel);
                                        $dataArea = array();
                                        $listArea = explode(",", (isset($row->area) ? $row->area : ""));
                                        foreach ($listArea as $area) {
                                            $dataArea[] = array(
                                                'id_area'    	    => \App\Area::where('name', $area)->first()->id,
                                                'id_pf'          	=> $fokus->id
                                            );
                                        }
                                        DB::table('fokus_areas')->insert($dataArea);
                                    } else {
                                        return false;
                                    }
                                    if (!isset($fokus->id)) {
                                        throw new Exception("Error Processing Request", 1);
                                    }
                                // }
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
		    	$data[] = array(
		    		'Product'		=> $val->product->name,
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
