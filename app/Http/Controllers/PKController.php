<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use Illuminate\Support\Str;
use App\ProductKnowledge;
use App\Position;
use DB;
class PKController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('productknowledge.PK');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['positions'] = Position::get();
        return view('productknowledge.PKCreate',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        $data=$request->all();
        $limit=[
            'filePDF'          => 'required|mimes:pdf|max:10000',
        ];
        $validator = Validator($data, $limit);
        if ($validator->fails()){
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        } else {
                 ProductKnowledge::create([
                'filePDF'           => $request->input('filePDF'),
                'admin'             => $request->input('admin'),
                'sender'            => $request->input('sender'),
                'subject'           => $request->input('subject'),
                'type'              => $request->input('type'),
                'target'            => $request->input('target'),
            ]); 
                return redirect()->route('pk')
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Product Knowledges!'
                ]); 
        } return redirect()->route('pk')
                ->with([
                    'type'      => 'danger',
                    'title'     => 'Gagal!<br/>',
                    'message'   => '<i class="em em-warning mr-2"></i>Gagal menambah Product!'
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
