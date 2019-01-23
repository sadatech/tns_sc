<?php

namespace App\Http\Controllers;

use App\Area;
use App\Region;
use App\SubCategory;
use App\Category;
use App\ProductStockType;
use App\Channel;
use App\ProductFokusMtc;
use App\Product;
use App\FokusArea;
use App\FokusChannel;
use App\FokusProduct;
use Exception;
use DB;
use Auth;
use File;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;

class ProductFokusMtcController extends Controller
{
	private $alert = [
		'type' => 'success',
		'title' => 'Sukses!<br/>',
		'message' => 'Berhasil melakukan aski.'
	];

	public function baca()
	{
		$data['area']		= Area::get();
		$data['product']	= Product::get();
		$data['channel']	= Channel::get();
		return view('product.fokusMtc', $data);
	}

	public function data()
	{
		$fokus = ProductFokusMtc::get();
		$product = array();
		$id = 1;
		foreach ($fokus as $doto) {
			$product[] = array(
				'id' => $id++,
				'id_pf' => $doto->id,
				'id_channel' => $doto->id_channel,
				'id_area' => $doto->id_area,
				'id_product' => $doto->id_product,
				'from' => $doto->from,
				'until' => $doto->to,
				'product' => (isset($doto->product->name) ? $doto->product->name : "-"),
				'channel' => (isset($doto->channel->name) ? $doto->channel->name : "-"),
				'area' => (isset($doto->area->name) ? $doto->area->name : "ALL"),
			);
		}
		return Datatables::of($product)
		->addColumn('action', function ($product) {
			$data = array(
				'id'            => $product['id_pf'],
				'product'     	=> (isset($product['id_product']) ? $product['id_product'] : ""),
				'channel'     	=> (isset($product['id_channel']) ? $product['id_channel'] : ""),
				'area'     	    => (isset($product['id_area']) ? $product['id_area'] : ""),
				'from'          => $product['from'],
				'to'          	=> $product['until']
			);
			return "<button onclick='editModal(".json_encode($data).")' class='btn btn-sm btn-primary btn-square' title='Update'><i class='si si-pencil'></i></button>
			<button data-url=".route('fokusMtc.delete', $product['id_pf'])." class='btn btn-sm btn-danger btn-square js-swal-delete' title='Delete'><i class='si si-trash'></i></button>";
		})->make(true);
	}

	public function store(Request $request)
	{
		$data = $request->all();

		if (($validator = ProductFokusMtc::validate($data))->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$from = explode('/', $data['from']);
		$data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
		$to = explode('/', $data['to']);
		$data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

		if (ProductFokusMtc::hasActivePF($data)) {
			$this->alert['type'] = 'warning';
			$this->alert['title'] = 'Warning!<br/>';
			$this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus MTC sudah ada!';
		} else {
			ProductFokusMtc::create($data);
			$this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk fokus MTC!';
		}
		return redirect()->back()->with($this->alert);
	}

	public function update(Request $request, $id)
	{
		$data = $request->all();
		$product = ProductFokusMtc::findOrFail($id);

		if (($validator = $product->validate($data))->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$from = explode('/', $data['from']); 
		$to = explode('/', $data['to']);
		$data['from'] = \Carbon\Carbon::create($from[1], $from[0])->startOfMonth()->toDateString();
		$data['to'] = \Carbon\Carbon::create($to[1], $to[0])->endOfMonth()->toDateString();

		if (ProductFokusMtc::hasActivePF($data, $product->id)) {
			$this->alert['type'] = 'warning';
			$this->alert['title'] = 'Warning!<br/>';
			$this->alert['message'] = '<i class="em em-confounded mr-2"></i>Produk fokus MTC sudah ada!';
		} else {
			$product->fill($data)->save();
			$this->alert['message'] = '<i class="em em-confetti_ball mr-2"></i>Berhasil mengubah product fokus MTC!';
		}
		return redirect()->back()->with($this->alert);
	}



	public function delete($id)
	{
		$product = ProductFokusMtc::find($id);
		$product->delete();
		return redirect()->back()
		->with([
			'type'      => 'success',
			'title'     => 'Sukses!<br/>',
			'message'   => '<i class="em em-confetti_ball mr-2"></i>Berhasil dihapus!'
		]);
	}

	public function importXLS(Request $request)
	{
		// try {
			$file = Input::file('file')->getClientOriginalName();
			$filename = pathinfo($file, PATHINFO_FILENAME);
			$extension = pathinfo($file, PATHINFO_EXTENSION);

			if ($extension != 'xlsx' && $extension !=  'xls') {
				return response()->json(['error' => 'true', 'error_detail' => "Error File Extention ($extension)"]);
			}

			if($request->hasFile('file')) {
				$file = $request->file('file')->getRealPath();
				$ext = '';
				Excel::filter('chunk')->selectSheetsByIndex(0)->load($file)->chunk(250, function($results) use($request) {
					// try {
						DB::beginTransaction();
						if (!empty($results->all())) {
							foreach($results as $row)
							{	
								// echo $row;
								$getSku = Product::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($row['sku']))."'")->first();
								if (($row['area'] == null)or(trim(strtoupper($row['area'])) == 'ALL')) {
									$getArea = null;
								}else{
									$getArea = Area::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($row['area']))."'")->first()->id;
								}
								$getChannel = Channel::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($row['channel']))."'")->first();
								$fokus = ProductFokusMtc::create([
									'id_product'    => $getSku->id,
									'id_area'       => $getArea,
									'id_channel'    => $getChannel->id,
									'from'  => \PHPExcel_Style_NumberFormat::toFormattedString($row['from'], 'YYYY-MM'),
									'to'    => \PHPExcel_Style_NumberFormat::toFormattedString($row['until'], 'YYYY-MM')
								]);
								// if (!isset($fokus->id)) {

								// } else {
								// 	$listProduct = explode(",", $row->sku);
        //                             // dd($listProduct);
								// 	foreach ($listProduct as $key => $product) {
								// 		$getSku = Product::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($product))."'")->first();
								// 		if (isset($getSku->id)) {
								// 			FokusProduct::create([
								// 				'id_pf'         => $fokus->id,
								// 				'id_product'    => $getSku->id
								// 			]);
								// 		}
								// 	}
								// 	$listChannel = explode(",", $row->channel);
								// 	foreach ($listChannel as $channel) {
								// 		FokusChannel::create([
								// 			'id_channel'    => $this->findChannel($channel),
								// 			'id_pf'         => $fokus->id
								// 		]);
								// 	}
								// 	$listArea = explode(",", $row->area);
								// 	foreach ($listArea as $area) {
								// 		$getArea = \App\Area::whereRaw("TRIM(UPPER(name)) = '".trim(strtoupper($area))."'")->first();
								// 		if (isset($getArea->id)) {
								// 			FokusArea::create([
								// 				'id_area'            => $getArea->id,
								// 				'id_pf'              => $fokus->id
								// 			]);
								// 		}
								// 	}
        //                             // dd($listArea);
        //                             // dd($getArea);
								// }
							}
							DB::commit();
						} else {
							throw new Exception("Error Processing Request", 1);
						}
					// } catch (Exception $e) {
					// 	DB::rollback();
					// 	return redirect()->back()->with([
					// 		'type' => 'danger',
					// 		'title' => 'Gagal!<br/>',
					// 		'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah produk target!'
					// 	]);
					// }
				}, false);
				return redirect()->back()->with([
					'type' => 'success',
					'title' => 'Sukses!<br/>',
					'message'=> '<i class="em em-confetti_ball mr-2"></i>Berhasil menambah produk target!'
				]);
			} else {
				DB::rollback();
				return redirect()->back()->with([
					'type' => 'danger',
					'title' => 'Gagal!<br/>',
					'message'=> '<i class="em em-confounded mr-2"></i>File harus di isi!'
				]);
			}
		// } catch (Exception $e) {
		// 	DB::rollback();
		// 	return redirect()->back()->with([
		// 		'type' => 'danger',
		// 		'title' => 'Gagal!<br/>',
		// 		'message'=> '<i class="em em-confounded mr-2"></i>Gagal menambah produk target!'
		// 	]);
		// }
	}

	public function findChannel($channel)
	{
		$dataChannel = Channel::whereRaw("TRIM(UPPER(name)) = '". trim(strtoupper($channel))."'");
		if ($dataChannel->count() == 0) {
			$data2 = Channel::create([
				'name'  => $channel
			]);
			if ($data2) {
				$id_channel = $data2->id;
			}
		} else {
			$id_channel = $dataChannel->first()->id;
		}
		return $id_channel;
	}

	public function export()
	{
		$emp = ProductFokusMtc::orderBy('created_at', 'DESC');
		if ($emp->count() > 0) {
			foreach ($emp->get() as $val) {
				// $area = FokusArea::where(
				// 	'id_pf', $val->id
				// )->get();
				// $areaList = array();
				// foreach($area as $dataArea) {
				// 	if(isset($dataArea->id_area)) {
				// 		$areaList[] = $dataArea->area->name;
				// 	} else {
				// 		$areaList[] = "-";
				// 	}
				// }
				// $channel = FokusChannel::where(
				// 	'id_pf', $val->id
				// )->get();
				// $channelList = array();
				// foreach($channel as $dataChannel) {
				// 	if(isset($dataChannel->id_channel)) {
				// 		$channelList[] = $dataChannel->channel->name;
				// 	} else {
				// 		$channelList[] = "-";
				// 	}
				// }
				// $product = FokusProduct::where(
				// 	'id_pf', $val->id
				// )->get();
				// $productList = array();
				// foreach($product as $dataProduct) {
				// 	if(isset($dataProduct->id_product)) {
				// 		$productList[] = $dataProduct->product->name;
				// 	} else {
				// 		$productList[] = "-";
				// 	}
				// }
				// $data[] = array(
				// 	'Product'		=> rtrim(implode(',', $productList), ','),
				// 	'Channel'	    => rtrim(implode(',', $channelList), ','),
				// 	'Area'			=> rtrim(implode(',', $areaList), ','),
				// 	'Month From'    => (isset($val->from) ? $val->from : "-"),
				// 	'Month Until'   => (isset($val->to) ? $val->to : "-")
				// );
				$data[] = array(
					'Product'		=> (isset($val->product->name) ? $val->product->name : "-"),
					'Channel'	    => (isset($val->channel->name) ? $val->channel->name : "-"),
					'Area'			=> (isset($val->area->name) ? $val->area->name : "ALL"),
					'Month From'    => (isset($val->from) ? $val->from : "-"),
					'Month Until'   => (isset($val->to) ? $val->to : "-")
				);
				// return response()->json($data);
			}

			$filename = "ProductFokus_".Carbon::now().".xlsx";
			return Excel::create($filename, function($excel) use ($data) {
				$excel->sheet('Employee', function($sheet) use ($data)
				{
					$sheet->fromArray($data);
				});
			})->download();
		} else {
			return redirect()->back()
			->with([
				'type'   => 'danger',
				'title'  => 'Gagal Unduh!<br/>',
				'message'=> '<i class="em em-confounded mr-2"></i>Data Kosong!'
			]);
		}
	}

}
