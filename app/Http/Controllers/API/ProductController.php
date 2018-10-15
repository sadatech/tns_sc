<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Price;
use App\Store;

class ProductController extends Controller
{
	public function listByCat($id, $store)
	{
		$store = Store::where('id',$store)->first()->type;
		$product = Product::where('id_category', $id)
		->with(['prices' => function($query) use ($store) {
			$query->where('type_toko', $store);
		}])->get();
		if (!empty($product)) {
			$dataArr = array();
			foreach ($product as $key => $pro) {
				if (isset($pro->prices[0]->price)) {
					$dataArr[] = array(
						'id' => $pro->id,
						'name' => $pro->name,
						'category' => $pro->category->name,
						'price' => $pro->prices[0]->price
					);
				} else {
					$dataArr[] = array(
						'id' => $pro->id,
						'name' => $pro->name,
						'category' => $pro->category->name,
						'price' => 0
					);
				}
			}
			$res['success'] = true;
			$res['product'] = $dataArr;
		} else {
			$res['success'] = false;
			$res['msg'] = "Gagal mengambil produk.";
		}
		return response()->json($res);
	}
}
