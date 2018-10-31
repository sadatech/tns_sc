<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FAQ;
use App\Position;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Auth;
use DB;
class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('faq.faq');        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $position = Position::get();
        return view('faq.faqcreate')->with('position',$position);
    }

    /**
     * store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        FAQ::create([
            'question'          => $request->input('question'),
            'answer'          => $request->input('answer'),
            'created_at'          => Carbon::now('Asia/Jakarta'),
            'updated_at'          => Carbon::now('Asia/Jakarta'),
            'answer'          => $request->input('answer'),
        ]);
        return redirect()->route('faq')
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah FAQ!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function data()
    {
     $faq = FAQ::select('faqs.*');;
     return Datatables::of($faq)
     ->addColumn('action', function ($faq) {
        return "<a href=".route('ubah.faq', $faq->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
        <button data-url=".route('faq.delete', $faq->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
    })
     ->rawColumns(['action','question','answer'])
     ->make(true);
 }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
     $faq = FAQ::findOrFail($id);
     return view('faq.faqupdate')->with('faq',$faq);
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
       $faq               = FAQ::find($id);
       $faq->question     = $request->get('question');
       $faq->answer       = $request->get('answer');
       $faq->updated_at       = Carbon::now('Asia/Jakarta');
       $faq->save();
       return redirect()->route('faq')
       ->with([
        'type'      => 'success',
        'title'     => 'Sukses!<br/>',
        'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil merubah FAQ!'
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
       $faq = FAQ::find($id);
       $faq->delete();
       return redirect()->back()
       ->with([
        'type'      => 'success',
        'title'     => 'Sukses!<br/>',
        'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
    ]);
   }
}
