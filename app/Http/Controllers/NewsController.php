<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use Yajra\Datatables\Datatables;
use App\Position;
use Auth;
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
         News::create([
            'sender'          => $request->input('sender'),
            'subject'          => $request->input('subject'),
            'content'          => $request->input('content'),
            'target'          => $request->input('target'),
        ]);
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
            ->join('positions', 'news.target', '=', 'positions.id')
              ->select('news.*', 'positions.name', 'news.target');
     
        return Datatables::of($news)
        ->addColumn('action', function ($news) {
            return "<a href=".route('ubah.news', $news->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('news.delete', $news->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })
        ->rawColumns(['action','sender','subject','content','target'])
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

        $news->sender = $request->get('sender');
        $news->subject = $request->get('subject');
        $news->content = $request->get('content');
        $news->target = $request->get('target');
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
