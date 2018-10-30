<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Area;
use App\Product;
use App\ProductFokusMd;
use Auth;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;

class ProductFokusMdController extends Controller
{
    private $alert = [
        'type' => 'success',
        'title' => 'Sukses!<br/>',
        'message' => 'Berhasil melakukan aski.'
    ];

    public function baca()
    {
        return view('product.fokusMD');
    }

    public function data()
    {
        $product = ProductFokusMd::with(['product','area'])
        ->select('product_fokus_mds.*');
        return Datatables::of($product)
        ->addColumn('area', function($product) {
			if (isset($product->area)) {
				$area = $product->area->name;
			} else {
				$area = "Without Area";
			}
			return $area;
		})
        ->addColumn('action', function ($product) {
            if (isset($product->area)) {
				$area = $product->area->id;
			} else {
				$area = "Without Area";
			}
            $data = array(
                'id'            => $product->id,
                'product'     	=> $product->product->id,
                'area'          => $area,
                'from'          => $product->from,
                'to'          	=> $product->to
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('fokusMD.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (($validator = ProductFokusMd::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $from = explode('/', $data['from']);
        $data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
        $to = explode('/', $data['to']);
        $data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

        if (ProductFokusMd::hasActivePF($data)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus MD sudah ada!';
        } else {
            ProductFokusMd::create($data);
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus MD!';
        }

        return redirect()->back()->with($this->alert);
    }

    public function update(Request $request, $id) 
    {
        $data = $request->all();
        $product = ProductFokusMd::findOrFail($id);

        if (($validator = $product->validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $from = explode('/', $data['from']); 
        $to = explode('/', $data['to']);
        $data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
        $data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

        if (ProductFokusMd::hasActivePF($data, $product->id)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus MD sudah ada!';
        } else {
            $product->fill($data)->save();
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus MD!';
        }

        return redirect()->back()->with($this->alert);
    }

    public function delete($id)
    {
        $product = ProductFokusMd::find($id);
            $product->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
}
