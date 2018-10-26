<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Auth;
use App\Position;

class PositionController extends Controller
{
	public function baca()
	{
		return view('employee.position');
	}

	public function data()
	{
		$position = Position::get();
		return Datatables::of($position)
		->addColumn('action', function ($position) {
			return '<button onclick="editModal('.$position->id.',&#39;'.$position->name.'&#39;)" class="btn btn-sm btn-primary btn-square"><i class="si si-pencil"></i></button>';
		})->make(true);
	}
	
	public function update(Request $request, $id) 
	{
		$position = Position::find($id);
			$position->name = $request->get('name');
			$position->save();
			return redirect()->back()
			->with([
				'type'    => 'success',
				'title'   => 'Sukses!<br/>',
				'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah position!'
			]);
	}

	public function delete($id) {
		$position = Position::find($id);
			$position->delete();
			return redirect()->back()
			->with([
				'type'    => 'success',
				'title'   => 'Sukses!<br/>',
				'message' => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
			]);
	}
}