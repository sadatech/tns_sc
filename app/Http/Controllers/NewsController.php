<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use Yajra\Datatables\Datatables;
use App\Position;
use Auth;
use Carbon\Carbon;
use DB;
class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     return view('news.news');
 }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $positions = Position::get();
        return view('news.newscreate')->with('positions',$positions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $news = new News;
        $news->sender = $request->input('sender');
        $news->subject = $request->input('subject');
        $news->content = $request->input('content');
        $news->created_at = Carbon::now('Asia/Jakarta');
        $news->updated_at = Carbon::now('Asia/Jakarta');
        if ($request->input('target') != 'All') {
            $news->target = $request->input('target');
        }
        $news->save();
        
        
        
        return redirect()->route('news')
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah News!'
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
        $news = DB::table('news')
        ->leftjoin('positions', 'news.target', '=', 'positions.id')
        ->select('news.*', 'positions.name');
        
        return Datatables::of($news)
        ->addColumn('action', function ($news) {
            return "<a href=".route('ubah.news', $news->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('news.delete', $news->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })
        ->rawColumns(['action','sender','subject','content','target'])
        ->editColumn('name',function($news){
            return $news->target==null ? 'All' : $news->name;
        })
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
        $data['positions'] = Position::get();
        $data['news'] = News::where(['id' => $id])->first();
        return view('news.newsupdate',$data);
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
        
        $news = News::findOrFail($id);

        $news->sender = $request->input('sender');
        $news->subject = $request->input('subject');
        $news->content = $request->input('content');
        $news->target = $request->target == 'All' ? null : $request->target;
        $news->updated_at = Carbon::now('Asia/Jakarta');
        $news->save();


        return redirect()->route('news')
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil merubah News!'
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
        $news = News::findOrFail($id);

        $news->delete();
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
        ]);
    }
}
