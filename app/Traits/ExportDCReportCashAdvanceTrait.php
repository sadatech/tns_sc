<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;

use App\CashAdvance;

trait ExportDCReportCashAdvanceTrait
{

	public function DCReportCashAdvanceExportTrait($id_area, $filtermonth, $filecode)
	{
		$data = [];
		$CashAdvanceDataQuery = CashAdvance::where("id_area", $id_area)
        ->whereMonth("date", Carbon::parse($filtermonth)->format("m"))
        ->whereYear("date", Carbon::parse($filtermonth)->format("Y"));
		$CashAdvanceData = (clone $CashAdvanceDataQuery)->get();

		$filename = "DC - Report Cash Advance (".$filecode.")";
		$store = Excel::create($filename, function($excel) use ($CashAdvanceDataQuery, $CashAdvanceData, &$data){
			$excel->sheet("Report", function($sheet) use ($CashAdvanceDataQuery, $CashAdvanceData, &$data){

	        	$dtObj = [];
	        	$dtObj["dataDB"] = $CashAdvanceData;

		        $data[] = $dtObj;

		        $dtObj["listHeader"][] = ["KM PADA SAAT PENGISIAN BBM", "ANGKUTAN", "BIAYA LAIN-LAIN"];
		        $dtObj["listHeader"][] = ["TGL",
		        	"KETERANGAN", "KM AWAL", "KM AKHIR", "KM TOTAL",
		        	"TPD", "HOTEL / KOSAN",
			        "BBM", "PARKIR/TOL", "PEMBELIAN BAHAN BAKU", "PEMBELIAN PROPERTY", "PERIJINAN",
			        "BUS", "SIPA", "OJEK", "BECAK", "TAKSI",
			        "RP.", "KETERANGAN", "TOTAL BIAYA"
		    	];

                // all width
                $dtObj["allWidth"] = "@";
                foreach ($dtObj["listHeader"][1] as $__header)
                {
                    $dtObj["allWidth"] = chr(ord($dtObj["allWidth"]) + 1);
                    $dtObj["allWidthObj"][] = $dtObj["allWidth"];
                }

                $startVal = 8;
                $dtObj["allHeightObj"] = [1, 2, 3, 4, 5, 6, 7, 8];
                foreach ($CashAdvanceData as $dbData)
                {
                	$dtObj["dataValue"][] = [
                		Carbon::parse($dbData->date)->format("d"),
                		$dbData->description,
                		$dbData->km_begin,
                		$dbData->km_end,
                		$dbData->km_distance,
                		$dbData->tpd,
                		$dbData->hotel,
                		$dbData->bbm,
                		$dbData->parking_and_toll,
                		$dbData->raw_material,
                		$dbData->property,
                		$dbData->permission,
                		$dbData->bus,
                		$dbData->sipa,
                		$dbData->taxibike,
                		$dbData->rickshaw,
                		$dbData->taxi,
                		$dbData->other_cost,
                		$dbData->other_description,
                		$dbData->total_cost,
                	];
                    $startVal++;
                    $dtObj["allHeightObj"][] = $startVal;
                    $dtObj["allHeight"] = $startVal;
                }

				for ($i=0; $i < 4; $i++) { 
	                $dtObj["allHeight"] = ($dtObj["allHeight"] + 1);
	                $dtObj["allHeightObj"][] = $dtObj["allHeight"];

	                if ($i == 3)
	                {
						$dtObj["dataValue"][] = [
							null,
							"Grand Total",
							(clone $CashAdvanceDataQuery)->sum("km_begin"),
							(clone $CashAdvanceDataQuery)->sum("km_end"),
							(clone $CashAdvanceDataQuery)->sum("km_distance"),
							(clone $CashAdvanceDataQuery)->sum("tpd"),
							(clone $CashAdvanceDataQuery)->sum("hotel"),
							(clone $CashAdvanceDataQuery)->sum("bbm"),
							(clone $CashAdvanceDataQuery)->sum("parking_and_toll"),
							(clone $CashAdvanceDataQuery)->sum("raw_material"),
							(clone $CashAdvanceDataQuery)->sum("property"),
							(clone $CashAdvanceDataQuery)->sum("permission"),
							(clone $CashAdvanceDataQuery)->sum("bus"),
							(clone $CashAdvanceDataQuery)->sum("sipa"),
							(clone $CashAdvanceDataQuery)->sum("taxibike"),
							(clone $CashAdvanceDataQuery)->sum("rickshaw"),
							(clone $CashAdvanceDataQuery)->sum("taxi"),
							(clone $CashAdvanceDataQuery)->sum("other_cost"),
							null,
							(clone $CashAdvanceDataQuery)->sum("total_cost"),
						];
	                } else $dtObj["dataValue"][] = [];
				}

                $dtObj["allHeight"] = ($dtObj["allHeight"] + 1);
                $dtObj["allHeightObj"][] = $dtObj["allHeight"];

                // skip line 1
                $sheet->row(1, function($row){
                	$row->setFontSize(11);
                });

                // header name // line 2
                $sheet->mergeCells($dtObj["allWidthObj"][0]."2:".$dtObj["allWidth"]."2");
                $sheet->cell($dtObj["allWidthObj"][0]."2", function($cell) use ($CashAdvanceDataQuery){
                	$cell->setValue("SUMMARY PENYELESAIAN CASH ADVANCE  - DEMO COOKING TEAM " . strtoupper((clone $CashAdvanceDataQuery)->first()->area->name));
                    $cell->setFontWeight('bold');
                    $cell->setFontSize(24);
                    $cell->setAlignment("center");
                });

                // skip line 3
                $sheet->row(3, function($row){
                	$row->setFontSize(11);
                });

	            // create all border
	            foreach ($dtObj["allWidthObj"] as $__allWidth)
	            {
	                foreach ($dtObj["allHeightObj"] as $__allHeight)
	                {
	                	if ($__allHeight > 7)
	                	{
		                    $sheet->cell($__allWidth.($__allHeight), function($cell){
		                        $cell->setBorder("thin", "thin", "thin", "thin");
		                    });
	                	}
	                }
	            }

	            // set width border
	            $sheet->setWidth("B", 30);
	            $sheet->setWidth("C", 15);
	            $sheet->setWidth("D", 15);
	            $sheet->setWidth("E", 15);
	            $sheet->setWidth("G", 20);
	            $sheet->setWidth("H", 15);
	            $sheet->setWidth("I", 20);
	            $sheet->setWidth("J", 25);
	            $sheet->setWidth("K", 25);
	            $sheet->setWidth("L", 15);
                $sheet->setWidth("R", 10);
                $sheet->setWidth("S", 30);
                $sheet->setWidth("T", 20);

                // create border 
                // Set all borders (top, right, bottom, left)
                $sheet->cell("R4", function($cell){
                	$cell->setValue("Area");
                    $cell->setAlignment("left");
                    $cell->setFontSize(11);
                	$cell->setBorder("thin", "none", "none", "thin");
                });
                $sheet->setWidth("S", 30);
                $sheet->cell("S4", function($cell) use ($CashAdvanceDataQuery){
                	$cell->setValue(": " . ucfirst((clone $CashAdvanceDataQuery)->first()->area->name));
                    $cell->setAlignment("left");
                    $cell->setFontSize(11);
                	$cell->setBorder("thin", "thin", "none", "none");
                });
                $sheet->cell("T4", function($cell){
                	$cell->setValue("");
                    $cell->setAlignment("left");
                    $cell->setFontSize(11);
                	$cell->setBorder("thin", "thin", "none", "none");
                });
                //
                $sheet->cell("R5", function($cell){
                	$cell->setValue("Periode");
                    $cell->setAlignment("left");
                    $cell->setFontSize(11);
                	$cell->setBorder("thin", "none", "thin", "thin");
                });
                $sheet->cell("S5", function($cell) use ($CashAdvanceDataQuery){
                	$cell->setValue(": " . Carbon::parse((clone $CashAdvanceDataQuery)->first()->date)->format("M Y"));
                    $cell->setAlignment("left");
                    $cell->setFontSize(11);
                	$cell->setBorder("thin", "none", "thin", "none");
                });
                $sheet->cell("T5", function($cell){
                	$cell->setValue("");
                    $cell->setAlignment("left");
                    $cell->setFontSize(11);
                	$cell->setBorder("thin", "thin", "thin", "none");
                });
                //
                $sheet->cell("S6", function($cell){
                	$cell->setValue("");
                    $cell->setAlignment("left");
                    $cell->setFontSize(11);
                });

                // create header 1
                $starthead = "@";
                foreach ($dtObj["listHeader"][1] as $Header2Key => $header2)
                {
                	$starthead = strtoupper(chr(ord($starthead) + 1));

                	if ($starthead == "C" || $starthead == "D" || $starthead == "E" ||
                		$starthead == "M" || $starthead == "N" || $starthead == "O" || $starthead == "P" || $starthead == "Q" ||
                		$starthead == "R" || $starthead == "S")
                	{
                		$dtObj["listSkip"][] = $starthead;
                	} else {
                		$dtObj["unListSkip"][] = $starthead;
                	}

                	foreach ($dtObj["unListSkip"] as $CharKey => $CharMerge)
                	{
                		$sheet->mergeCells($CharMerge."8:".$CharMerge."9");
                	}

            		$cellFill = ($starthead == "C" || $starthead == "D" || $starthead == "E" || $starthead == "M" || $starthead == "N" || $starthead == "O" || $starthead == "P" || $starthead == "Q" || $starthead == "R" || $starthead == "S" ? $starthead."9" : $starthead."8");

            		$sheet->row(8, function($row){
	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                    $row->setFontSize(11);
            		});

            		$sheet->cell($cellFill, function($cell) use ($cellFill, $Header2Key, $dtObj){
                		$cell->setValue($dtObj["listHeader"][1][$Header2Key]);
	                    $cell->setAlignment("center");
	                    $cell->setValignment("center");
	                    $cell->setFontSize(11);
            		});

                }

                $sheet->mergeCells("C8:E8");
                $sheet->cell("C8", function($cell) use ($dtObj){
            		$cell->setValue($dtObj["listHeader"][0][0]);
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                    $cell->setFontSize(11);
                });
                $sheet->mergeCells("M8:Q8");
                $sheet->cell("M8", function($cell) use ($dtObj){
            		$cell->setValue($dtObj["listHeader"][0][1]);
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                    $cell->setFontSize(11);
                });
                $sheet->mergeCells("R8:S8");
                $sheet->cell("R8", function($cell) use ($dtObj){
            		$cell->setValue($dtObj["listHeader"][0][2]);
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                    $cell->setFontSize(11);
                });

                $startNum = 10;
                foreach ($dtObj["dataValue"] as $dtValue)
                {
                	$sheet->row($startNum, $dtValue);
                	$startNum++;
                }

                $data = $dtObj;

			});
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}

}