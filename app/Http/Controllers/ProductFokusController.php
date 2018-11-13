<?php

namespace App\Http\Controllers;

use App\Area;
use App\Region;
use App\SubCategory;
use App\Category;
use App\ProductStockType;
use App\Product;
use App\Channel;
use App\FokusChannel;
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
                'product'     	=> $product->product->id,
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

                $product->fill($data)->save();

                $oldChanel = $product->fokus->pluck('id_channel');
                $deleteChannel = $oldChanel->diff($channel);
                foreach ($deleteChannel as $deleted_id) {
                    FokusChannel::where([
                        'id_pf'         => $product->id,
                        'id_channel'    => $deleted_id])->delete(); 
                }

                foreach ($channel as $channel_id) {
                    FokusChannel::updateOrCreate([
                        'id_pf'         => $product->id,
                        'id_channel'    => $channel_id
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

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' =>   'required'
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
                        $dataProduct['product']           = $row->sku;
                        $dataProduct['code']              = $row->code;
                        $dataProduct['panel']             = $row->panel;
                        $dataProduct['carton']            = (isset($row->carton) ? $row->carton : "");
                        $dataProduct['pack']              = (isset($row->pack) ? $row->pack : "");
                        $dataProduct['type']              = $row->type;
                        $dataProduct['subcategory']       = $row->subcategory;
                        $dataProduct['category']          = $row->category;
                        $id_product = $this->findProduct($dataProduct);

                        $insert = ProductFokus::create([
                            'id_product'        => $id_product,
                            'from'             => (isset($row->from) ? $row->from : "-"),
                            'to'                => (isset($row->to) ? $row->to : "-")
                        ]);
                        if ($insert) {
                            $dataChannel = array();
                            $listChannel = explode(",", $row->channel);
                            foreach ($listChannel as $channel) {
                                $dataChannel[] = array(
                                    'id_channel'    	=> $this->findChannel($channel),
                                    'id_pf'          	=> $insert->id
                                );
                            }
                            DB::table('fokus_channels')->insert($dataChannel);

                            $dataArea = array();
                            $listArea = explode(",", (isset($row->area) ? $row->area : ""));
                            foreach ($listArea as $area) {
                                $dataArea[] = array(
                                    'id_area'    	    => $this->findArea($area, $row->region),
                                    'id_pf'          	=> $insert->id
                                );
                            }
                            DB::table('fokus_areas')->insert($dataArea);
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
        }else{
            return redirect()->back()
            ->with([
                'type'    => 'danger',
                'title'   => 'Gagal!<br/>',
                'message' => '<i class="em em-warning mr-2"></i>Gagal import!'
            ]);
        }
    }

    public function findProduct($data)
    {
        // $dataProduct = Product::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['product']))."'");
        // if ($dataProduct->count() == 0) {

			$data2['subcategory']   	= $data['subcategory'];
			$data2['category']   	    = $data['category'];
            $id_subcategory = $this->findSub($data2);

            $getType    = ProductStockType::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($data['type']))."'")->first()->id;
            $data1      = Product::create([
                'name'       	    => $data['product'],
				'code'              => $data['code'],
				'panel'		        => ($data['panel'] ? $data['panel'] : "yes"),
				'carton'		    => (isset($data['carton']) ? $data['carton'] : ""),
                'pack'	            => (isset($data['pack']) ? $data['pack'] : ""),
                'pcs'               => 1,
                'id_brand'          => 1,
                'stock_type_id'     => ($getType ? $getType : 1 ),
                'id_subcategory'    => $id_subcategory

            ]);
            if ($data1) {
                $id_pasar = $data1->id;
            }
        // } else {
        //     $id_pasar = $dataProduct->first()->id;
        // }
        return $id_pasar;
    }

    public function findSub($subcategory)
    {
        $dataChannel = SubCategory::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($subcategory['subcategory']))."'");
        if ($dataChannel->count() == 0) {

            $dataCategory = $subcategory['category'];
            $id_category = $this->findCategory($dataCategory);

            $channel = SubCategory::create([
                'name'              => $subcategory['subcategory'],
                'id_category'       => $id_category
            ]);
            if ($channel) {
                $id_channel = $channel->id;
            }
        } else {
            $id_channel = $dataChannel->first()->id;
        }
        return $id_channel;
    }

    public function findCategory($category)
    {
        $dataChannel = Category::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($category['category']))."'");
        if ($dataChannel->get() != null) {
            $data2 = Category::create([
                'name'              => $category['category'],
                'description'       => "-"
            ]);
            if ($data2) {
                $id_channel = $data2->id;
            }
        } else {
            $id_channel = $dataChannel->first()->id;
        }
        return $id_channel;
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

    public function findArea($area, $region)
    {
        $dataChannel = Area::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($area))."'");
        if ($dataChannel->count() == 0) {

            $dataRegion = $region;
            $id_region = $this->findRegion($dataRegion);

            $channel = Area::create([
                'name'          => $area,
                'id_region'     => $id_region
            ]);
            if ($channel) {
                $id_channel = $channel->id;
            }
        } else {
            $id_channel = $dataChannel->first()->id;
        }
        return $id_channel;
    }

    public function findRegion($region)
    {
        $dataRegion = Region::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($region))."'");
        if ($dataRegion->count() == 0) {

            $channel = Region::create([
                'name'          => $region,
            ]);
            if ($channel) {
                $id_channel = $channel->id;
            }
        } else {
            $id_channel = $dataRegion->first()->id;
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
