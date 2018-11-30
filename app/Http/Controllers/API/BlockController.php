<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\Block;
use Carbon\Carbon;
use JWTAuth;
use Config;
use DB;
class BlockController extends Controller
{
	use ApiAuthHelper;
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}
	public function store(Request $request)
	{
		
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;
			if (empty($request->name) || empty($request->subarea) ) {
				$res['success'] = false;
				$res['msg'] = "Sub Area and Name cannot be empty.";
			} else {
				if (!empty(SubArea::find($request->subarea))) {
					$insert = Block::create([
						'id_subarea'	=> $request->subarea,
						'id_employee'	=> $user->id,
						'name'			=> $request->name,
						'phone'			=> $request->phone,
						'address'		=> $request->address,
						'active'		=> 1,
					]);
					if ($insert->id) {
						$res['success'] = true;
						$res['msg'] 	= "Success add blocks.";
					} else {
						$res['success'] = false;
						$res['msg'] 	= "Failed to add blocks.";
					}
				} else {
					$res['success'] = false;
					$res['msg'] 	= "Sub Area tidak bisa ditermukan.";
				}
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
	public function update(Request $request, $id)
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;
			$update = Block::where('id',$id)->update([
				'name'		=> $request->name,
				'phone'		=> $request->phone,
				'address'	=> $request->address,
			]);
			if ($update) {
				$res['success'] = true;
				$res['msg'] 	= "Success update blocks.";
			} else {
				$res['success'] = false;
				$res['msg'] 	= "Failed to update blocks.";
			}
			
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
	public function list($active = 1)
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;
			if ($active == 1 || $active == 2) {
				if ($active == 1) {
					$activeStatus = "aktif";
				}else{
					$activeStatus = "tidak aktif";
				}
				$block = Block::where('active', $active)->where('id_employee', $user->id)->get();
				if ($block->count() > 0) {
					$listBlock = [];
					$res['success'] = true;
					foreach ($block as $data) {
						$listBlock[] = array(
							'id' 			=> $data->id,
							'name' 			=> $data->name,
							'phone' 		=> $data->phone,
							'address'		=> $data->address,
							'id_subarea' 	=> $data->subarea->id,
							'subarea_name' 	=> $data->subarea->name,
						);
					}
					$res['block'] = $listBlock;
				} else {
					$res['success'] = false;
					$res['msg'] = "Kamu tidak mempunyai Block $activeStatus.";
				}
			} else {
				$res['success'] = false;
				$res['msg'] = "Tipe Block tidak diketahui.";
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
	public function disable($id, $status)
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;
			if (empty($id)) {
				$res['success'] = false;
				$res['msg'] = "Please select block.";
			} else if( empty($status) ) {
				$res['success'] = false;
				$res['msg'] = "Please set block status.";
			} else {
				DB::transaction(function() use ($id, $status, &$res){
					if ($status == 'true') {
						$update = Block::where("id", $id)
						->update([
							'active'	=> 2,
						]);
						$disableStatus = 'disable';
					} else {
						$update = Block::where("id", $id)
						->update([
							'active'	=> 1,
						]);
						$disableStatus = 'enable';
					}
					if ($update) {
						$res['success'] = true;
						$res['msg'] = "Success $disableStatus blocks.";
					}else{
						$res['success'] = false;
						$res['msg'] = "Fail $disableStatus blocks.";
					}
				});
			}
			
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}