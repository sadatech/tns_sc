<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;
use Illuminate\Support\Collection;

use App\Area;
use App\Account;
use App\Category;

trait ExportMTCAvailabilityTrait
{

	public function MTCAvailabilityExportTrait($limitArea, $limitAccount, $filecode)
	{
		$categories = Category::get();

		$headerList[] = ["NO", "AREA"];
		$headerList[] = ["NO", "ACCOUNT"];

		/**
		 * Merge header
		 */
		foreach ($categories as $category)
		{
			$headerList[0][] = strtoupper($category->name);
			$headerList[1][] = strtoupper($category->name);
		}

		/**
		 * Value Area & Account
		 */
		$categories = Category::get();
		$areas = Area::get();
		$accounts = Account::get();

		foreach ($areas as $area)
		{
			$item['id'] = $area->id;
			$item['area'] = $area->name;
			$x = 2;
			foreach ($categories as $category)
			{
				$totalProduct = DB::select(
					"
					SELECT COUNT(dv.id) as data_count
					FROM detail_availability dv
					JOIN availability a ON dv.id_availability = a.id
					JOIN stores s ON a.id_store = s.id
					JOIN sub_areas sa ON s.id_subarea = sa.id
					JOIN areas ar ON sa.id_area = ar.id
					JOIN products p ON dv.id_product = p.id
					JOIN sub_categories sc ON p.id_subcategory = sc.id
					JOIN categories c ON sc.id_category = c.id
					WHERE c.id = '".$category->id."'
					AND ar.id = '".$area->id."'
					")[0]->data_count * 1;
				$totalProductAvailability = DB::select(
					"
					SELECT COUNT(dv.id) as data_count
					FROM detail_availability dv
					JOIN availability a ON dv.id_availability = a.id
					JOIN stores s ON a.id_store = s.id
					JOIN sub_areas sa ON s.id_subarea = sa.id
					JOIN areas ar ON sa.id_area = ar.id
					JOIN products p ON dv.id_product = p.id
					JOIN sub_categories sc ON p.id_subcategory = sc.id
					JOIN categories c ON sc.id_category = c.id
					WHERE c.id = '".$category->id."'
					AND ar.id = '".$area->id."'
					AND dv.available = 1
					")[0]->data_count * 1;
                // return response()->json(round($totalProductAvailability / $totalProduct, 2) * 100);
				if ($totalProductAvailability == 0) {
					$total = 0;
				}else{
					$total = round($totalProductAvailability / $totalProduct, 2) * 100; 
				}
				$item['item_'.$category->name] = $total;
				$x++;
			}
			$valueList["area"][] = $item;
		}

		foreach ($accounts as $account)
		{
			$item['id'] = $account->id;
			$item['area'] = $account->name;
			$x = 2;
			foreach ($categories as $category)
			{
				$totalProduct = DB::select(
					"
					SELECT COUNT(dv.id) as data_count
					FROM detail_availability dv
					JOIN availability a ON dv.id_availability = a.id
					JOIN stores s ON a.id_store = s.id
					JOIN accounts ac ON s.id_account = ac.id
					JOIN products p ON dv.id_product = p.id
					JOIN sub_categories sc ON p.id_subcategory = sc.id
					JOIN categories c ON sc.id_category = c.id
					WHERE c.id = '".$category->id."'
					AND ac.id = '".$account->id."'
					")[0]->data_count * 1;
				$totalProductAvailability = DB::select(
					"
					SELECT COUNT(dv.id) as data_count
					FROM detail_availability dv
					JOIN availability a ON dv.id_availability = a.id
					JOIN stores s ON a.id_store = s.id
					JOIN accounts ac ON s.id_account = ac.id
					JOIN products p ON dv.id_product = p.id
					JOIN sub_categories sc ON p.id_subcategory = sc.id
					JOIN categories c ON sc.id_category = c.id
					WHERE c.id = '".$category->id."'
					AND ac.id = '".$account->id."'
					AND dv.available = 1
					")[0]->data_count * 1;
				if ($totalProductAvailability == 0)
				{
					$total = 0;
				}else{
					$total = round($totalProductAvailability / $totalProduct, 2) * 100; 
				}
				$item['item_'.$category->name] = $total;
				$x++;
			}
			$valueList["account"][] = $item;
		}

		
		/**
		 * Excel
		 */
		$filename = "MTC Availability - ".Carbon::now()->format("F Y")." (".$filecode.")";
		$XLS = Excel::create($filename, function($excel) use ($categories, $headerList, $valueList){
			$excel->setTitle("MTC Availability - ".Carbon::now()->format("F Y"));
			$excel->setCreator("SADA Technologies");
			$excel->setCompany("SADA Technologies");
			$excel->setLastModifiedBy("SADA Technologies");

			$excel->sheet("Data", function($sheet) use ($categories, $headerList, $valueList){

                $dtObj["regCell"] = [];

                // count basic header
                $dtObj["headerWidth1"] = "@";
                for ($i=0; $i < count($headerList[0]); $i++)
                {
                    $dtObj["headerWidth1"] = chr(ord($dtObj["headerWidth1"]) + 1);
                    $dtObj["headerWidth1Obj"][] = $dtObj["headerWidth1"];
                    $dtObj["regCell"][] = $dtObj["headerWidth1"]."2";
                    $sheet->setWidth($dtObj["headerWidth1"], ($dtObj["headerWidth1"] == "B" ? 20 : 15));
                }

                // skip row 1
                $sheet->row(1, function($row){
                    $row->setFontSize(11);
                });

                // area
                $sheet->row(2, $headerList[0]);
                $sheet->row(2, function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontSize(12);
                    $row->setFontWeight('bold');
                });

                // area value
                $xlsRowVal = 3;
                foreach ($valueList["area"] as $valueArea)
                {
                	$sheet->row($xlsRowVal, $valueArea);
                	$sheet->row($xlsRowVal, function($row){
                        $row->setAlignment("left");
                	});

                	$dtObj["regCell"][] = "A".$xlsRowVal;
                	$dtObj["regCell"][] = "B".$xlsRowVal;
                	$rowStartChar = "C";
                	foreach ($categories as $category)
                	{
	                	$dtObj["regCell"][] = $rowStartChar.$xlsRowVal;
                		$rowStartChar = chr(ord($rowStartChar) + 1);
                	}

                	$xlsRowVal++;
                }

                $xlsRowVal = ($xlsRowVal + 2);

                // count basic header
                $dtObj["headerWidth2"] = "@";
                for ($i=0; $i < count($headerList[1]); $i++)
                {
                    $dtObj["headerWidth2"] = chr(ord($dtObj["headerWidth2"]) + 1);
                    $dtObj["headerWidth2Obj"][] = $dtObj["headerWidth2"];
                    $dtObj["regCell"][] = $dtObj["headerWidth2"].$xlsRowVal;
                }

                $sheet->row($xlsRowVal, $headerList[1]);
                $sheet->row($xlsRowVal, function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontSize(12);
                    $row->setFontWeight('bold');
                });

                $xlsRowVal = ($xlsRowVal + 1);

                // account value
                foreach ($valueList["account"] as $accountArea)
                {
                	$sheet->row($xlsRowVal, $accountArea);
                	$sheet->row($xlsRowVal, function($row){
                        $row->setAlignment("left");
                	});

                	$dtObj["regCell"][] = "A".$xlsRowVal;
                	$dtObj["regCell"][] = "B".$xlsRowVal;
                	$rowStartChar = "C";
                	foreach ($categories as $category)
                	{
	                	$dtObj["regCell"][] = $rowStartChar.$xlsRowVal;
                		$rowStartChar = chr(ord($rowStartChar) + 1);
                	}

                	$xlsRowVal++;
                }


                // create border
                foreach($dtObj["regCell"] as $regCellBorder)
                {
                    $sheet->cell($regCellBorder, function($cell){
                        $cell->setBorder("thin", "thin", "thin", "thin");
                    });
                }

			});
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}

}
