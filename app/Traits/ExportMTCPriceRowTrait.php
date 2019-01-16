<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\SubArea;
use App\Store;
use App\Product;
use App\DataPrice;
use App\ProductCompetitor;

use Illuminate\Database\Eloquent\Collection;

trait ExportMTCPriceRowTrait
{
	protected $request, $priceRowData, $filecode;
	
	public function MTCPriceRowExportTrait($request, $filecode)
	{
		$this->prirceRowData = [];
		$this->request = $request;
		$this->filecode = $filecode;
		
		$this->collectDataRow();
		return $this->createXLS($this->xlsHelper());
	}

	private function createXLS($PriceRowData)
	{
		$filename = 'MTC - Price ROW - '.Carbon::parse('01/'.$this->request['periode'])->format('F').' '.Carbon::parse('01/'.$this->request['periode'])->format('Y') . " (" . $this->filecode . ")";
		$excel = Excel::create($filename, function($excel) use ($PriceRowData){
            /** Set information */
            $excel->setTitle('MTC - Report Price Row');
            $excel->setCreator('SADA');
            $excel->setCompany('SADA');
            $excel->setDescription('MTC - Report Price Row');

            /** */
            $excel->getDefaultStyle()
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            /**  */
            $excel->sheet('PRICE ROW', function ($sheet) use ($PriceRowData){
                $sheet->setHeight(1, 25);
                $sheet->fromModel($PriceRowData, null, 'A1', true, true);
                $sheet->row(1, function ($row){
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:H1', function($cells){
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:H1', 'thin');
            });
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
	
	private function xlsHelper()
	{
		$stores = Store::where('stores.id_account',$this->request['account'])->orderBy('id_subarea')->get();
		$collection = collect($this->priceRowData)->take($this->request['limitLs']);
		
		return $collection->map(function($item) use ($stores){
			$listData = [
				"CATEGORY"  => @$item['category_name'],
				"PRODUCT"   => @$item['brand_name'],
				"PACKAGING" => @$item['name'],
			];
			
			foreach($stores as $store)
			{
				$listData[strtoupper($store->name1)] = @$item[$store->name1."_price"];
			}
			
			$listData["LOWEST"]  = @$item['lowest'];
			$listData["HIGHEST"] = @$item['highest'];
			$listData["HIGHEST VS LOWEST"] = @$item['vs'];
			
			return $listData;
		});
	}
	
	private function collectDataRow()
	{
		if (!empty($this->request['periode']))
		{
			$date = explode('/', $this->request['periode']);
			$year   = $date[1];
			$month  = $date[0];
		}
		else
		{
			$year   = Carbon::now()->format('Y');
			$month  = Carbon::now()->format('m');
		}
		
		if (!empty($this->request['account']))
		{
			$account   = $this->request['account'];
		}
		else
		{
			$account   = '1';
		}
		
		$subareas = SubArea::get();
		$stores = Store::where('stores.id_account',$account)->orderBy('id_subarea')->get();
		// ->pluck('stores.id');
		
		$datas1 = Product::join('brands','products.id_brand','brands.id')
		->join('sub_categories','products.id_subcategory','sub_categories.id')
		->join('categories','sub_categories.id_category', 'categories.id')
		->select('products.*',
		'brands.name as brand_name',
		'categories.name as category_name')
		->orderBy('categories.id', 'ASC')->get();
		
		foreach ($datas1 as $data1) {
			$data1['lowest'] = '';
			$data1['highest'] = '';
			$data1['vs'] = '';
			
			foreach ($stores as $store ) {
				$data1[$store->name1.'_price'] = '';
				$price = DataPrice::where('data_price.id_store',$store->id)
				->whereMonth('data_price.date', $month)
				->whereYear('data_price.date', $year)
				->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
				->where('detail_data_price.id_product',$data1->id)
				->where('detail_data_price.isSasa',1)->first();
				
				
				if($price){
					$data1[$store->name1.'_price'] = $price->price;
					
					if (($data1['lowest'] == '')&&($data1[$store->name1.'_price'] != null)) {
						$data1['lowest'] = $data1[$store->name1.'_price'];
						$data1['highest'] = $data1[$store->name1.'_price'];
						
					}
					if(($data1['lowest'] > $data1[$store->name1.'_price'])&&($data1[$store->name1.'_price'] != null)){
						$data1['lowest'] = $data1[$store->name1.'_price'];
					}
					if(($data1['highest'] < $data1[$store->name1.'_price'])&&($data1[$store->name1.'_price'] != null)){
						$data1['highest'] = $data1[$store->name1.'_price'];
					}
				}
			}
			
			// }
			if ($data1['lowest'] != '') {
				$data1['vs'] = round($data1['highest'] / $data1['lowest'] * 1, 2);
			}
		}        
		
		
		$datas2 = ProductCompetitor::join('brands','product_competitors.id_brand','brands.id')
		->join('sub_categories','product_competitors.id_subcategory','sub_categories.id')
		->join('categories','sub_categories.id_category', 'categories.id')
		->select('product_competitors.*',
		'brands.name as brand_name',
		'categories.name as category_name')
		->orderBy('categories.id', 'ASC')->get();
		
		
		foreach ($datas2 as $data2) {
			$data2['lowest'] = '';
			$data2['highest'] = '';
			$data2['vs'] = '';
			
			foreach ($stores as $store ) {
				$data2[$store->name1.'_price'] = '';
				$price = DataPrice::where('data_price.id_store',$store->id)
				->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
				->where('detail_data_price.id_product',$data2->id)
				->where('detail_data_price.isSasa',0)->first();
				// return response()->json($price);
				
				
				if($price){
					$data2[$store->name1.'_price'] = $price->price;
					
					if (($data2['lowest'] == '')&&($data2[$store->name1.'_price'] != null)) {
						$data2['lowest'] = $data2[$store->name1.'_price'];
						$data2['highest'] = $data2[$store->name1.'_price'];
						
					}
					if(($data2['lowest'] > $data2[$store->name1.'_price'])&&($data2[$store->name1.'_price'] != null)){
						$data2['lowest'] = $data2[$store->name1.'_price'];
					}
					if(($data2['highest'] < $data2[$store->name1.'_price'])&&($data2[$store->name1.'_price'] != null)){
						$data2['highest'] = $data2[$store->name1.'_price'];
					}
				}
				
			}
			if ($data2['lowest'] != '') {
				$data2['vs'] = round($data2['highest'] / $data2['lowest'] * 1, 2);
			}
			$this->priceRowData = $datas1->push($data2); // Contains foo and bar.
		}
	}
	
}