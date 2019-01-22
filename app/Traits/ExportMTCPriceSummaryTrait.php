<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\Account;
use App\SubArea;
use App\Store;
use App\Product;
use App\DataPrice;
use App\ProductCompetitor;

use Illuminate\Database\Eloquent\Collection;

trait ExportMTCPriceSummaryTrait
{
	protected $request, $priceSummaryData, $filecode;
	
	public function MTCPriceSummaryExportTrait($request, $filecode)
	{
		$this->priceSummaryData = [];
		$this->request = $request;
		$this->filecode = $filecode;
		
		$this->collectDataSummary();
		return $this->createXLS($this->xlsHelper());
	}

	private function createXLS($PriceSummaryData)
	{
		$filename = 'MTC - Price Summary - '.Carbon::parse('01/'.$this->request['periode'])->format('F').' '.Carbon::parse('01/'.$this->request['periode'])->format('Y') . " (" . $this->filecode . ")";
		$excel = Excel::create($filename, function($excel) use ($PriceSummaryData){
            /** Set information */
            $excel->setTitle('MTC - Report Price Summary');
            $excel->setCreator('SADA');
            $excel->setCompany('SADA');
            $excel->setDescription('MTC - Report Price Summary');

            /** */
            $excel->getDefaultStyle()
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            /**  */
            $excel->sheet('PRICE SUMMARY', function ($sheet) use ($PriceSummaryData){

				$sheet->setHeight(1, 20);
				$sheet->setHeight(2, 20);

				$sheet->mergeCells("A1:A2");
				$sheet->cell("A1", "CATEGORY");
				$sheet->setWidth("A", 25);
				$sheet->mergeCells("B1:B2");
				$sheet->cell("B1", "PRODUCT");
				$sheet->setWidth("B", 25);
				$sheet->mergeCells("C1:C2");
				$sheet->cell("C1", "PACKAGING");
				$sheet->setWidth("C", 25);

				$row1Char = "D";
				foreach(Account::get() as $account)
				{
					$sheet->mergeCells($row1Char."1:".chr(ord($row1Char) + 1)."1");
					$sheet->cell(chr(ord($row1Char))."1", strtoupper($account->name));
					$sheet->cell(chr(ord($row1Char))."2", "LOWEST");
					$sheet->cell(chr(ord($row1Char) + 1)."2", "HIGHEST");
					$row1Char = chr(ord($row1Char) + 2);
				}
				$row1Char = chr(ord($row1Char) - 1);

				$sheet->mergeCells(chr(ord($row1Char) + 1)."1:".chr(ord($row1Char) + 1)."2");
				$sheet->cell(chr(ord($row1Char) + 1)."1", "LOWEST");
				$sheet->setWidth(chr(ord($row1Char) + 1), 25);

				$sheet->mergeCells(chr(ord($row1Char) + 2)."1:".chr(ord($row1Char) + 2)."2");
				$sheet->cell(chr(ord($row1Char) + 2)."1", "HIGHEST");
				$sheet->setWidth(chr(ord($row1Char) + 2), 25);

				$sheet->mergeCells(chr(ord($row1Char) + 3)."1:".chr(ord($row1Char) + 3)."2");
				$sheet->cell(chr(ord($row1Char) + 3)."1", "HIGHEST VS LOWEST");
				$sheet->setWidth(chr(ord($row1Char) + 3), 25);

                $sheet->row(1, function ($row){
					$row->setBackground('#82abde');
                    $row->setAlignment("center");
                    $row->setValignment("center");
					$row->setFontWeight('bold');
				});
                $sheet->row(2, function ($row){
					$row->setBackground('#82abde');
                    $row->setAlignment("center");
                    $row->setValignment("center");
					$row->setFontWeight('bold');
				});

				$valueRow = 3;
				foreach($PriceSummaryData as $PriceSummaryItem)
				{
					$sheet->row($valueRow, $PriceSummaryItem);
					$valueRow++;
				}
            });
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
	
	private function xlsHelper()
	{
		$accounts = Account::get();
		$collection = collect($this->priceSummaryData)->take($this->request['limitLs']);

		$collection = $collection->map(function($item) use ($accounts){
			$listData = [
				@$item['category_name'],
				@$item['brand_name'],
				@$item['name'],
			];
			
			foreach($accounts as $account)
			{
				$listData[] = @$item[$account->id."_min"];
				$listData[] = @$item[$account->id."_max"];
			}
			
			$listData[]  = @$item['lowest'];
			$listData[] = @$item['higest'];
			$listData[] = @$item['vs'];
			
			return $listData;
		});

		return $collection;
	}
	
	private function collectDataSummary()
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
        $subareas = SubArea::get();
        $accounts = Account::get();

        $datas2 = Product::join('brands','products.id_brand','brands.id')
                        ->join('sub_categories','products.id_subcategory','sub_categories.id')
                        ->join('categories','sub_categories.id_category', 'categories.id')
                        ->select('products.*',
                            'brands.name as brand_name',
                            'categories.name as category_name')
                        ->orderBy('category_name')->get();

        foreach ($datas2 as $data2) {
            $data2['lowest'] = '';
            $data2['highest'] = '';
            $data2['vs'] = '';

            foreach ($accounts as $account) {
                // $data2[$subarea->id.'_min'] = '-';
                // $data2[$subarea->id.'_max'] = '-';

                $store = Store::where('stores.id_account',$account->id)
                            ->pluck('stores.id');
                $price = DataPrice::whereIn('data_price.id_store',$store)
                                ->whereMonth('data_price.date', $month)
                                ->whereYear('data_price.date', $year)
                                ->join('detail_data_price','data_price.id','detail_data_price.id_data_price')
                                ->where('detail_data_price.id_product',$data2->id)
                                ->where('detail_data_price.isSasa',1);
                if($price){
                    $storeMin = $price->where('price', $price->min('price'))->pluck('id_store');
                    $location = Store::whereIn('stores.id',$storeMin)
                                    ->pluck('stores.name1')->toArray();
                    $data2[$account->id.'store_min'] = implode(", ",$location);

                    $storeMax = $price->where('price', $price->max('price'))->pluck('id_store');
                    $location = Store::whereIn('stores.id',$storeMax)
                                    ->pluck('stores.name1')->toArray();
                    $data2[$account->id.'store_max'] = implode(", ",$location);

                    $data2[$account->id.'_min'] = $price->min('price');
                    $data2[$account->id.'_max'] = $price->max('price');

                    if (($data2['lowest'] == '')&&($data2[$account->id.'_min'] != null)) {
                            $data2['lowest'] = $data2[$account->id.'_min'];
                            $data2['highest'] = $data2[$account->id.'_max'];
                        
                    }
                    if(($data2['lowest'] > $data2[$account->id.'_min'])&&($data2[$account->id.'_min'] != null)){
                        $data2['lowest'] = $data2[$account->id.'_min'];
                    }
                    if(($data2['highest'] < $data2[$account->id.'_max'])&&($data2[$account->id.'_max'] != null)){
                        $data2['highest'] = $data2[$account->id.'_max'];
                    }
                }

            }
            if ($data2['lowest'] != '') {
                $data2['vs'] = round($data2['highest'] / $data2['lowest'] * 1, 2);
            }
		}  
		$this->priceSummaryData = $datas2; // Contains foo and bar.
	}
	
}