<?php

namespace App\Http\Controllers;

use App\SkuUnit;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SkuUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function baca()
    {
        return view('product.sku_unit');
    }

    public function data()
    {
        $skuUnit = SkuUnit::get();
        return Datatables::of($skuUnit)
        ->addColumn('action', function ($row) {
            $data = $row->toArray();
            return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
            <button data-url=".route('sub-category.delete', $row->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $skuUnit = new SkuUnit;
        if (($validator = $skuUnit->validate($request->all()))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $skuUnit->fill($request->all())->save();
        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah SKU Unit!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $skuUnit = SkuUnit::findOrFail($id);

        if (($validator = $skuUnit->validate($request->all()))->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $skuUnit->fill($request->all())->save();
        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah SKU Unit!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SkuUnit::findOrFail($id)->delete();

        return redirect()->back()->with([
            'type' => 'success',
            'title' => 'Sukses!<br/>',
            'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menghapus SKU Unit!'
        ]);
    }
}
