<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\SubArea;
use App\Account;
use App\Store;
use App\Product;
use App\DataPrice;
use App\Category;
use App\ProductCompetitor;
use App\OosDetail;

use Illuminate\Database\Eloquent\Collection;

trait ExportMTCOosRowTrait
{
	protected $request, $OosRowData, $filecode;
	
	public function MTCOosRowExportTrait($request, $filecode)
	{
		$this->OosRowData = [];
		$this->request = $request;
		$this->filecode = $filecode;
		
		$this->collectDataRow();
		return $this->createXLS($this->xlsHelper());
	}

	private function createXLS($OosRowData)
	{
		$filename = 'MTC - OOS ROW - '.Carbon::parse('01/'.$this->request['periode'])->format('F').' '.Carbon::parse('01/'.$this->request['periode'])->format('Y') . " (" . $this->filecode . ")";
		$excel = Excel::create($filename, function($excel) use ($OosRowData){
            /** Set information */
            $excel->setTitle('MTC - Report OOS Row');
            $excel->setCreator('SADA');
            $excel->setCompany('SADA');
            $excel->setDescription('MTC - Report OOS Row');

            /** */
            $excel->getDefaultStyle()
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            /**  */
            $excel->sheet('AVAILABILITY ROW', function ($sheet) use ($OosRowData){

				$sheet->setHeight(1, 20);
				$sheet->setHeight(2, 20);

				$sheet->mergeCells("A1:A2");
				$sheet->cell("A1", "DATE");
				$sheet->setWidth("A", 25);
				$sheet->mergeCells("B1:B2");
				$sheet->cell("B1", "STORE NAME");
				$sheet->setWidth("B", 25);
				$sheet->mergeCells("C1:C2");
				$sheet->cell("C1", "ACCOUNT");
				$sheet->setWidth("C", 25);
				$sheet->mergeCells("D1:D2");
				$sheet->cell("D1", "AREA");
				$sheet->setWidth("D", 25);
				$sheet->mergeCells("E1:E2");
				$sheet->cell("E1", "CEK/NO");
				$sheet->setWidth("E", 25);

				$row1Char = "F";
				foreach(Category::get() as $category)
				{
					$productCount = Product::join('sub_categories','products.id_subcategory','sub_categories.id')
		              ->join('categories','sub_categories.id_category', 'categories.id')
		              ->where('categories.id',$category->id)
		              ->count();
					$sheet->mergeCells($row1Char."1:".chr(ord($row1Char) + $productCount-1)."1");
					$sheet->cell(chr(ord($row1Char))."1", strtoupper($category->name));
					foreach(Product::join('sub_categories','products.id_subcategory','sub_categories.id')
			              ->join('categories','sub_categories.id_category', 'categories.id')
			              ->where('categories.id',$category->id)->select('products.*')->get() as $product)
					{
						$sheet->cell(chr(ord($row1Char))."2", strtoupper($product->name));
						$row1Char = chr(ord($row1Char) + 1);
					}
					// $row1Char = chr(ord($row1Char) + $productCount);
				}
				$row1Char = chr(ord($row1Char) - 1);


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
				foreach($OosRowData as $OosRowItem)
				{
					$sheet->row($valueRow, $OosRowItem);
					$valueRow++;
				}
            });

            /**  */
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
	
	private function xlsHelper()
	{
		$categories = Category::get();
		$collection = collect($this->OosRowData)->take($this->request['limitLs']);

		$collection = $collection->map(function($item) use ($categories){
			$listData = [
				@$item['oos_date'],
				@$item['name1'],
				@$item['account_name'],
				@$item['area_name'],
				@$item['cek'],
			];
			
			foreach($categories as $category)
			{
		      foreach(Product::join('sub_categories','products.id_subcategory','sub_categories.id')
		        ->join('categories','sub_categories.id_category', 'categories.id')
		        ->where('categories.id',$category->id)->select('products.*')->get() as $product)
		      {
				$listData[] = @$item[$category->id."_".$product->id];
		      }
			}
			
			return $listData;
		});

		return $collection;
	}
	
	private function collectDataRow()
	{
        if (!empty($this->request['account'])) {
            $account   = $this->request['account'];
        }else{
            $account   = Account::first()->id;
        }
        $categories = Category::get();

        $datas = Store::where('stores.id_account',$account)
                        ->join('oos','stores.id','oos.id_store')
                        // ->join('oos_details','oos.id','oos_details.id_oos')
                        ->leftjoin('accounts','stores.id_account','accounts.id')
                        ->leftjoin('sub_areas','stores.id_subarea','sub_areas.id')
                // ->when($this->request['employee'], function ($q){
                //     return $q->where('display_shares.id_employee',$this->request['employee']);
                // })
                ->when($this->request['periode'], function ($q){
                    return $q->whereMonth('date', substr($this->request['periode'], 0, 2))
                    ->whereYear('date', substr($this->request['periode'], 3));
                })
                ->when(!empty($this->request['store']), function ($q){
                    return $q->where('id_store', $this->request['store']);
                })
                ->when($this->request['area'], function ($q){
                    return $q->where('id_area', $this->request['area']);
                })
                ->when($this->request['week'], function ($q){
                    return $q->where('availability.week', $this->request['week']);
                })
                        ->select(
                            'stores.id',
                            'oos.date as oos_date',
                            'stores.name1',
                            'stores.name2',
                            'accounts.name as account_name',
                            'sub_areas.name as area_name',
                            'oos.id as oos_id'
                            )
                        ->orderBy('oos_date')
                        ->get();

        // return response()->json($datas);
        foreach($datas as $data) {
                        $data['cek'] = 'NO';
            foreach ($categories as $category) {
                $data[$category->id] = $category->name;
                $data[$category->id.'sum'] = 0;
                $data[$category->id.'sumAvailable'] = 0;
                $products = Product::join('sub_categories','products.id_subcategory','sub_categories.id')
                                ->join('categories','sub_categories.id_category', 'categories.id')
                                ->join('product_stock_types','products.stock_type_id','product_stock_types.id')
                                ->where('categories.id',$category->id)
                                ->select('products.*','product_stock_types.quantity as type_qty')->get();
                foreach ($products as $product) {
                    $data[$category->id.'_'.$product->id] = '-';
                    $detail_data = OosDetail::where('oos_details.id_oos', $data->oos_id)
                                                    ->where('oos_details.id_product',$product->id)
                                                    ->first();
                    if ($detail_data) {
                        if ($detail_data->qty >= $product->type_qty) {
                            $data[$category->id.'_'.$product->id] = 1;
                        }else{
                            $data[$category->id.'_'.$product->id] = 0;
                        }
                        $data[$category->id.'sumAvailable'] += $data[$category->id.'_'.$product->id];
                        $data[$category->id.'sum'] += 1;
                        $data['cek'] = 'CEK';
                    }

                }
                    if ($data[$category->id.'sum'] > 0){
                        $data[$category->id.'oos'] = round($data[$category->id.'sumAvailable'] / $data[$category->id.'sum'] * 100, 2).'%';

                    }else{
                        $data[$category->id.'oos'] = 'mobile';
                    }
            }

        } 


		$this->OosRowData = $datas; // Contains foo and bar.
	}
	
}