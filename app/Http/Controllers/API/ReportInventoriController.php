<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Components\traits\ApiAuthHelper;
use App\ReportInventori;
use App\PropertiDc;
use JWTAuth;
use Config;
use Image;
use DB;

class ReportInventoriController extends Controller
{
	use ApiAuthHelper;

	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}
	public function store(Request $request, $id)
	{
		
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;
			if (empty($request->properti_dc) ) {
				$res['success'] = false;
				$res['msg'] = "Property DC cannot be empty.";
			} else {
				if (!empty(PropertiDc::find($request->properti_dc))) {

					if ($image 	= $request->file('photo')) {
						$photo 	= time()."_".$image->getClientOriginalName();
						$path 	= 'uploads/report_inventory';
						$image->move($path, $photo);
						$image_compress = Image::make($path.'/'.$photo)->orientate();
						$image_compress->save($path.'/'.$photo, 50);
					}
					$insert = ReportInventori::where('id',$id)->update([
						'quantity'			=> $request->quantity,
						'actual'			=> $request->actual,
						'status'			=> $request->status,
						'photo'				=> isset($photo) ? $path.'/'.$photo : null,
					]);
					if ($insert->id) {
						$res['success'] = true;
						$res['msg'] 	= "Success add Report Inventory.";
					} else {
						$res['success'] = false;
						$res['msg'] 	= "Failed to add Report Inventory.";
					}
				} else {
					$res['success'] = false;
					$res['msg'] 	= "Property DC undefined.";
				}
			}
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
	public function list()
	{
		$check = $this->authCheck();
		if ($check['success'] == true) {
			$user = $check['user'];
			unset($check['user']);
			$res = $check;

			$reportInventory = ReportInventori::where('id_employee', $user->id)->get();

			if ($reportInventory->count() > 0) {
				$listReportInventory = [];
				$res['success'] = true;
				foreach ($reportInventory as $data) {
					$listReportInventory[] = array(
						'id' 				=> $data->id,
						'name' 				=> $data->name,
						'quantity' 			=> $data->quantity,
						'actual'			=> $data->actual,
						'status'			=> $data->status,
						'photo'				=> $data->photo,
						'photo_url'			=> $data->photo ? asset($data->photo) : null,
						'id_properti_dc' 	=> $data->properti->id,
						'properti_dc_name' 	=> $data->properti->name,
					);
				}
				$res['reportInventory'] = $listReportInventory;
			} else {
				$res['success'] = false;
				$res['msg'] = "Kamu tidak mempunyai Report Inventori.";
			}
			
		}else{
			$res = $check;
		}
		$code = $res['code'];
		unset($res['code']);
		return response()->json($res, $code);
	}
}