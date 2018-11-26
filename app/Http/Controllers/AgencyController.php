<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Filters\AgencyFilters;
use App\Agency;
use Auth;
use App\Employee;

class AgencyController extends Controller
{
    public function getDataWithFilters(AgencyFilters $filters)
    {
        $data = Agency::filter($filters)->get();
        return $data;
    }
    
    public function baca()
    {
        return view('employee.agency');
    }

    public function data()
    {
        $agency = Agency::get();
        return Datatables::of($agency)
        ->addColumn('name', function($agency) {
            return $agency->name;
        })
        ->addColumn('action', function ($agency) {
            return '<button onclick="editModal('.$agency->id.',&#39;'.$agency->name.'&#39;)" class="btn btn-sm btn-primary btn-square"><i class="si si-pencil"></i></button>
            <button data-url='.route("agency.delete", $agency->id).' class="btn btn-sm btn-danger btn-square js-swal-delete"><i class="si si-trash"></i></button>';
        })->make(true);
    }
    
    public function store(Request $request)
    {   
        Agency::create([
            'name'          => $request->input('name'),
        ]);
        return redirect()->back()
        ->with([
            'type'      => 'success',
            'title'     => 'Sukses!<br/>',
            'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah agency!'
        ]);
    }

    public function update(Request $request, $id) 
    {
        $agency = Agency::find($id);
          $agency->name = $request->get('name');
          $agency->save();
          return redirect()->back()
          ->with([
              'type'    => 'success',
              'title'   => 'Sukses!<br/>',
              'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah agency!'
          ]);
      }

    public function delete($id) 
    {
        $agency = Agency::find($id);
            $emp = Employee::where(['id_agency' => $agency->id])->count();
            if (!$emp < 1) {
                return redirect()->back()
                ->with([
                    'type'    => 'danger',
                    'title'   => 'Gagal!<br/>',
                    'message' => '<i class="em em-warning mr-2"></i> Data ini tidak dapat dihapus karena terhubung dengan data lain di Employee!'
                ]);
            } else {
                $agency->delete();
                return redirect()->back()
                ->with([
                    'type'      => 'success',
                    'title'     => 'Sukses!<br/>',
                    'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
               ]);
            }
    }
}
