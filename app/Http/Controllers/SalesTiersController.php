<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\SalesTiers;
use Yajra\Datatables\Datatables;
use App\Filters\SalesTierFilters;

class SalesTiersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('store.sales');
    }

    public function getDataWithFilters(SalesTierFilters $filters){
        $data = SalesTiers::filter($filters)->get();
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function data()
    {
       $sales = SalesTiers::select('sales_tiers.*');;
        return Datatables::of($sales)
        ->addColumn('action', function ($sales) {
            return "<button onclick='editModal(".json_encode($sales).")' class='btn btn-sm btn-primary btn-square'><i class='si si-pencil'></i></button>
            <button data-url=".route('sales_tiers.delete', $sales->id)." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='si si-trash'></i></button>";
        })
        ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        SalesTiers::create([
            'name'          => $request->input('name'),
        ]);
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Sales!'
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
          $sales = SalesTiers::find($id);
          $sales->name = $request->get('name');
          $sales->save();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil merubah Sales!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $sales = SalesTiers::find($id);
        $sales->delete();
        return redirect()->back()
        ->with([
            'type' => 'success',
            'title' => 'Sukses! <br>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menghapus Sales!'
        ]);
    }
}
