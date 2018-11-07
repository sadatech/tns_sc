<?php

namespace App\Http\Controllers;

use App\Area;
use App\Product;
use App\FokusChannel;
use App\FokusArea;
use App\ProductFokus;
use Auth;
use DB;
use Illuminate\Http\Request;
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
        return view('product.fokus');
    }

    public function data()
    {
        $product = ProductFokus::with(['product','fokusarea','fokus', ])
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
}
