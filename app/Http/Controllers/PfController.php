<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;
use App\Category;
use App\SubCategory;
use App\Pf;

class PfController extends Controller
{
    public function read()
    {
        $data['category'] = SubCategory::get();
        return view('product.pf', $data);
    }

    public function data()
    {
        $product = Pf::with(['category1', 'category2'])
        ->select('pfs.*');
        return Datatables::of($product)
        ->addColumn('category1', function($product) {
            return SubCategory::where('id', $product->id_category1)->first()->name;
        })
        ->addColumn('category2', function($product) {
            return SubCategory::where('id', $product->id_category2)->first()->name;
        })
        ->addColumn('action', function ($product) {
            $category1 = SubCategory::where('id', $product->id_category1)->first()->id;
            $category2 = SubCategory::where('id', $product->id_category2)->first()->id;
            $data = array(
                'id'            => $product->id,
                'category1'     => $category1,
                'category2'     => $category2,
                'from'          => $product->from,
                'to'          	=> $product->to
            );
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('pf.delete', $product->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if (($validator = Pf::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $from = explode('/', $data['from']);
        $data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
        $to = explode('/', $data['to']);
        $data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

        if (Pf::hasActivePF($data)) {
            $this->alert['type'] = 'warning';
            $this->alert['title'] = 'Warning!<br/>';
            $this->alert['message'] = '<i class="em em-confounded mr-2"></i>Setting Produk fokus sudah ada!';
        } else {
            DB::transaction(function () use($data) {
                $product = Pf::create($data);
            });
            $this->alert['type'] = 'success';
            $this->alert['title'] = 'Berhasil!<br/>';
            $this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus!';
        }

        return redirect()->back()->with($this->alert);
    }

    public function update(Request $request, $id) 
    {
        $product = Pf::findOrFail($id);
        $data = $request->all();
        if (($validator = Pf::validate($data))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use($product, $data) {
            $product->fill($data)->save();
        });

        return redirect()->back()->with([
            'type'    => 'success',
            'title'   => 'Sukses!<br/>',
            'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product!'
        ]);
    }

    public function delete($id)
    {
        $product = Pf::find($id);
            $product->delete();
            return redirect()->back()
            ->with([
                'type'      => 'success',
                'title'     => 'Sukses!<br/>',
                'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
            ]);
    }
}
