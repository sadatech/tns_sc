<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\ReportInventori;
use App\EmployeeSubArea;

trait ExportDCReportInventoriTrait
{

	public function DCReportInventoriExportTrait($filecode, $employee, $area)
	{
		$data = [];

		// 
        $filename = "DC - Report Inventori (".$filecode.")";
        $store = Excel::create($filename, function($excel) use (&$data, $employee, $area){
        // $reportData
        $datas = ReportInventori::orderBy('report_inventories.created_at', 'DESC')

        ->when($employee, function($q) use ($employee)
        {
            return $q->where("report_inventories.id_employee", $employee);
        })
        ->when($area, function($q) use ($area)
        {
            return $q->join('employees','report_inventories.id_employee','employees.id')
                        ->join('employee_sub_areas','employees.id','employee_sub_areas.id_employee')
                        ->join('sub_areas','employee_sub_areas.id_subarea','sub_areas.id')
                        ->where('sub_areas.id_area', $area)
                        ->select('report_inventories.*');
        })
        ->get();

	        // foreach (ReportInventori::groupBy("id_employee")->get() as $ReportInventori)
	        foreach ($datas as $ReportInventori)
	        {
		        // get area
		        $EmployeeSubAreaName = EmployeeSubArea::where("id_employee", $ReportInventori->id_employee)->first()->subarea->area->name;

		        $excel->sheet("Area " . $EmployeeSubAreaName, function($sheet) use ($ReportInventori, $EmployeeSubAreaName, &$data){

		        	$dtObj = [];
		        	$dtObj["dataDB"] = ReportInventori::where("id_employee", $ReportInventori->id_employee);

		        	$dtObj["dataDBCount"] = $dtObj["dataDB"]->count();

		        	$dtObj["listHeader"] = ["No", "Item", "Quantity", "Actual", "Status", "Description", "Dokumentasi"];

	                // all width
	                $dtObj["allWidth"] = "@";
	                foreach ($dtObj["listHeader"] as $__header)
	                {
	                    $dtObj["allWidth"] = chr(ord($dtObj["allWidth"]) + 1);
	                    $dtObj["allWidthObj"][] = $dtObj["allWidth"];
	                }

	                // list value
	                $startVal = 9;
	                $dtObj["allHeightObj"] = [1, 2, 3, 4, 5, 6, 7, 8, 9];
	                $dtObj["imgCoordinate"] = [];
	                $g = 0;
	                foreach ($dtObj["dataDB"]->get() as $dataDB_data)
	                {
	                	$g++;
	                    $dtObj["dataValue"][$startVal] = [
	                        $g,
	                        $dataDB_data->properti->item,
	                        $dataDB_data->quantity,
	                        $dataDB_data->actual,
	                        (isset($dataDB_data->status) ? $dataDB_data->status : "-"),
	                        (isset($dataDB_data->description) ? $dataDB_data->description : "-"),
	                        (isset($dataDB_data->photo) ? null : "-")
	                    ];
	                	$dtObj["imgCoordinate"][$startVal]["Drawing"] = new PHPExcel_Worksheet_Drawing;
	                	if (isset($dataDB_data->photo))
	                	{
	                		$dtObj["imgCoordinate"][$startVal]["Drawing"]->setPath(public_path("/../../public_html/".($dataDB_data->photo)));
	                		$dtObj["imgCoordinate"][$startVal]["Drawing"]->setCoordinates("G".($startVal - 1));
	                		$dtObj["imgCoordinate"][$startVal]["Drawing"]->setWorksheet($sheet);
	                		$dtObj["imgCoordinate"][$startVal]["Drawing"]->setWidth(40);
	                	}
	                    $startVal++;
	                    $dtObj["allHeightObj"][] = $startVal;
	                    $dtObj["allHeight"] = $startVal;
	                }

	                for ($i=0; $i < 2; $i++)
	                { 
		                $pj = (count($dtObj["allHeightObj"]) - 1);
		                unset($dtObj["allHeightObj"][$pj]);
	                }


		            // create all border
		            foreach ($dtObj["allWidthObj"] as $__allWidth)
		            {
		                foreach ($dtObj["allHeightObj"] as $__allHeight)
		                {
		                	if ($__allHeight > 6)
		                	{
			                    $sheet->cell($__allWidth.($__allHeight), function($cell){
			                        $cell->setBorder("thin", "thin", "thin", "thin");
			                    });
		                	}
		                }
		            }

		        	$sheet->setWidth("B", 43);
		        	$sheet->setWidth("C", 14);
		        	$sheet->setWidth("D", 14);
		        	$sheet->setWidth("E", 18);
		        	$sheet->setWidth("F", 15);

		        	$sheet->mergeCells("A2:B2");
		        	$sheet->row(2, ["Data Properti Cooking Demo Nasional"]);
		        	$sheet->row(2, function($row){
	                    $row->setFontWeight('bold');
	                    $row->setFontSize(16);
		        	});

		        	$sheet->row(3, ["NoPolisi", $ReportInventori->no_polisi]);
		        	$sheet->row(3, function($row){
		        		$row->setAlignment("left");
	                    $row->setFontWeight('bold');
	                    $row->setFontSize(12);
		        	});

		        	$sheet->row(4, ["Area", ": " . $EmployeeSubAreaName]);
		        	$sheet->row(4, function($row){
	                    $row->setFontSize(12);
		        	});

		        	$sheet->row(5, ["Nama TL", ": " . $ReportInventori->employee->name]);
		        	$sheet->row(5, function($row){
	                    $row->setFontSize(12);
		        	});

		        	$sheet->row(6, function($row){
	                    $row->setFontSize(12);
		        	});

		        	$sheet->row(7, $dtObj["listHeader"]);

		        	$data[] = $dtObj;
		        	$sheet->setHeight(7, 25);
		        	$sheet->row(7, function($row){
	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                    $row->setFontWeight('bold');
	                    $row->setFontSize(13);
	                    $row->setBackground("#bfbfbf");
		        	});

		        	// isi value
	                $startRow = 7;
	                foreach ($dtObj["dataValue"] as $__dataValue)
	                {
	                    $startRow++;
	                    $sheet->row($startRow, $__dataValue);
	                }

	                foreach ($dtObj["allWidthObj"] as $__allWidth)
	                {
		                $sheet->cells($__allWidth."8:".$__allWidth."".($dtObj["allHeight"] + 1), function($cell) use ($__allWidth){
		                    $cell->setAlignment("center");
		                    $cell->setValignment("center");
		                	if ($__allWidth == "B")
		                	{
			                    $cell->setAlignment("left");
			                    $cell->setValignment("center");
		                	}
		                	$cell->setFontSize(12);
		                });
	                }

	                $sheet->mergeCells("A" . ($dtObj["allHeight"] + 2) . ":" . "B" . ($dtObj["allHeight"] + 2));
	                $sheet->cell("A" . ($dtObj["allHeight"] + 2), function($row){
	                	$row->setValue("Jakarta, " . Carbon::now()->format("d M Y"));
	                	$row->setFontSize(12);
	                });

	                $sheet->mergeCells("B" . ($dtObj["allHeight"] + 4) . ":" . "D" . ($dtObj["allHeight"] + 4));
	                $sheet->cell("A" . ($dtObj["allHeight"] + 4), function($row){
	                	$row->setValue("Check By");
	                	$row->setFontWeight('bold');
	                	$row->setFontSize(12);
	                });
	                $sheet->cell("B" . ($dtObj["allHeight"] + 4), function($row){
	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                	$row->setValue("Acknowledge");
	                	$row->setFontWeight('bold');
	                	$row->setFontSize(12);
	                });

	                $sheet->cell("A" . ($dtObj["allHeight"] + 9), function($row){
	                	$row->setValue("Team Leader");
	                	$row->setFontSize(12);
	                });
	                $sheet->cell("B" . ($dtObj["allHeight"] + 9), function($row){
	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                	$row->setValue("PM Sasa Rama Indonesia");
	                	$row->setFontSize(12);
	                });
	                $sheet->mergeCells("C" . ($dtObj["allHeight"] + 9) . ":" . "D" . ($dtObj["allHeight"] + 9));
	                $sheet->cell("C" . ($dtObj["allHeight"] + 9), function($row) use ($EmployeeSubAreaName){
	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                	$row->setValue("FPM / ASM Area " . $EmployeeSubAreaName);
	                	$row->setFontSize(12);
	                });

		        });

	        }

        })->store("xlsx", public_path("export/report"), true);

       return asset("export/report") . "/" . $filename . ".xlsx";
	}

}