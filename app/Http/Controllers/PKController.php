<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use Carbon\Carbon;
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

        $pk = new ProductKnowledge;
        $data = $request->all();
        $files = $request->file('fileku');
            if ($request->hasFile('fileku')) {
                $filename = $files->getClientOriginalName();
                $file_path = 'uploads/PKFilePDF';
                $files->move($file_path, $filename);
                $pk->filePDF = $filename;
        }
        if ( $pk->subject = $request->input('subject')==NULL) {
             return redirect()->route('pk')
                ->with([
                    'type'   => 'danger',
                    'title'  => 'Gagal!<br/>',
                    'message'=> '<i class="em em-confounded mr-2"></i>Harap isi Subject!'
                ]);
        }
        $pk->admin = $request->input('admin');
        $pk->sender = $request->input('sender');
       
        $pk->type = $request->input('type');
        $pk->created_at = Carbon::now('Asia/Jakarta');
        $pk->updated_at = Carbon::now('Asia/Jakarta');
        if ($request->input('target') != 'All') {
            $pk->target = $request->input('target');
        }
        $pk->save();
                return redirect()->route('pk')
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah Product Knowledges!'
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

    public function data()
    {
           $pk = DB::table('product_knowledges')
            ->leftjoin('positions', 'product_knowledges.target', '=', 'positions.id')
              ->select('product_knowledges.*', 'positions.name', 'product_knowledges.target');
     
        return Datatables::of($pk)
        ->addColumn('action', function ($pk) {
            return "<a href=".route('ubah.pk', $pk->id)." class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></a>
            <button data-url=".route('pk.delete', $pk->id)." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
        })
        ->editColumn('filePDF', function ($item) {
                    if($item->filePDF != "") {
                        return "<a target='_blank' href='uploads/PKFilePDF/" . $item->filePDF . "' class='btn btn-sm btn-danger'><i class='fa fa-file-pdf-o'></i> &nbsp; Download PDF</a>";
                    }else{
                        return "<label class='btn btn-sm btn-primary'>No File Uploaded</label>";
                    }

                })
        ->rawColumns(['action','admin','sender','subject','filePDF','target'])
         ->editColumn('name',function($pk){
            return $pk->target==null ? 'All' : $pk->name;
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
        $data['pk'] = ProductKnowledge::where(['id' => $id])->first();
        return view('productknowledge.PKUpdate',$data);
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
            //return $request->all();
            //return $request->file('fileku')->getClientOriginalName();
            $pk = ProductKnowledge::find($id);
            $files = $request->file('fileku');
            if ($request->hasFile('fileku')) {
                $filename = $files->getClientOriginalName();
                $file_path = 'uploads/PKFilePDF';
                $files->move($file_path, $filename);
                $pk->filePDF = $filename;
            }

            $pk->admin = $request->input('admin');
            $pk->sender = $request->input('sender');
            $pk->subject = $request->input('subject');
            $pk->target = $request->target == 'All' ? null : $request->target;
            $pk->save();
            // $file = $files->getClientOriginalExtension();
            
            
             return redirect()->route('pk')
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil merubah Product Knowledges!'
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
       $pk = ProductKnowledge::find($id);
       $pk->delete();

          return redirect()->route('pk')
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menghapus Product Knowledges!'
                ]); 
    }
}
