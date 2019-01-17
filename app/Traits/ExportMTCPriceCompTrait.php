<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\SubArea;
use App\SubCategory;
use App\Store;
use App\Product;
use App\DataPrice;
use App\ProductCompetitor;

use Illuminate\Database\Eloquent\Collection;

trait ExportMTCPriceCompTrait
{
	protected $request, $priceCompData, $filecode;
	
	public function MTCPriceCompExportTrait($request, $filecode)
	{
		$this->request = $request;
		$this->filecode = $filecode;
		
		$this->collectDataRow();
		return $this->createXLS($this->xlsHelper());
	}
	
	private function createXLS($PriceCompData)
	{
		$filename = 'MTC - Price VS Competitor - '.Carbon::parse('01/'.$this->request['periode'])->format('F').' '.Carbon::parse('01/'.$this->request['periode'])->format('Y') . " (" . $this->filecode . ")";
		$excel = Excel::create($filename, function($excel) use ($PriceCompData){
			/** Set information */
			$excel->setTitle('MTC - Report Price VS Competitor');
			$excel->setCreator('SADA');
			$excel->setCompany('SADA');
			$excel->setDescription('MTC - Report Price VS Competitor');
			
			/** */
			$excel->getDefaultStyle()
			->getAlignment()
			->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			/**  */
			$excel->sheet('PRICE VS COMPETITOR', function ($sheet) use ($PriceCompData){
				$sheet->setHeight(1, 25);
				$sheet->fromModel($PriceCompData, null, 'A1', true, true);
				$sheet->row(1, function ($row){
					$row->setBackground('#82abde');
				});
				$sheet->cells('A1:G1', function($cells){
					$cells->setFontWeight('bold');
				});
				$sheet->setBorder('A1:G1', 'thin');
			});
		})->store("xlsx", public_path("export/report"), true);
		
		return asset("export/report") . "/" . $filename . ".xlsx";
	}
	
	private function xlsHelper()
	{
		$collection = collect($this->priceCompData)->take($this->request['limitLs']);
		
		return $collection->map(function($item){
			$listData = [
				"CATEGORY"  => @$item['category_name'],
				"SASA"  => @$item['name'],
				"MAIN KOMPETITOR"  => @$item['competitor_name'],
				"BRAND KOMPETITOR"  => @$item['competitor_brand'],
				"SASA PRICE"  => @$item['price'],
				"KOMPETITOR PRICE"  => @$item['price_competitor'],
				"INDEX"  => @$item['index'],
			];
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
		
		if (!empty($this->request['store']))
		{
			$store   = $this->request['store'];
		}
		else
		{
			$store   = '1';
		}
		
		$products = Product::join('brands','products.id_brand','brands.id')
		->join('sub_categories','products.id_subcategory','sub_categories.id')
		->join('categories','sub_categories.id_category','categories.id')
		->select('products.*','brands.name as brand_name','categories.name as category_name')->get();
		
		foreach ($products as $product)
		{
			$product['competitor_name'] = '';
			$product['competitor_brand'] = '';
			$product['price'] = '';
			$product['price_competitor'] = '';
			$product['index'] = '';
			
			$competitors = ProductCompetitor::where('product_competitors.id', $product->id_main_competitor)
			->join('brands','product_competitors.id_brand','brands.id')
			->select('product_competitors.*','brands.name as brand_name_competitor')->first();
			
			$price = DataPrice::where('data_price.id_store', $store)
			->whereMonth('data_price.date', $month)
			->whereYear('data_price.date', $year)
			->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
			->where('detail_data_price.id_product',$product->id)
			->where('detail_data_price.isSasa',1)->first();
			
			if ($price)
			{
				$product['price'] = $price->price;
			}
			
			if ($competitors)
			{
				$product['competitor_name'] = $competitors->name;
				$product['competitor_brand'] = $competitors->brand_name_competitor;
				$priceCompetitor = DataPrice::where('data_price.id_store', $store)
				->whereMonth('data_price.date', $month)
				->whereYear('data_price.date', $year)
				->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
				->where('detail_data_price.id_product',$competitors->id)
				->where('detail_data_price.isSasa',0)->first();
				if ($priceCompetitor)
				{
					$product['price_competitor'] = $priceCompetitor->price;
					
					if($product['price']>0)
					{
						$product['index'] = abs($product['price']-$product['price_competitor']); 
					}
				}
			}
		}
		
		$this->priceCompData = $products;
	}
	
}