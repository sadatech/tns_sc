<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Brand;
use App\Price;
use App\Store;

class BrandController extends Controller
{
	public function __construct()
	{
		Config::set('auth.providers.users.model', \App\Employee::class);
	}

	public function list()
	{
		$brands = Brand::get();
		if ($brands->count() > 0) {
			$dataArr = array();
			foreach ($brands as $key => $brand) {
				$dataArr[] = array(
					'id' 		=> $brand->id,
					'name' 		=> $brand->name,
					'keterangan'=> $brand->keterangan,
				);
			}
			$res['success'] = true;
			$res['brand'] 	= $dataArr;
		} else {
			$res['success'] = false;
			$res['msg'] 	= "Gagal mengambil brand.";
		}
		return response()->json($res);
	}
}
