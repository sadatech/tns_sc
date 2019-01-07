<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\Employee;
use App\EmployeeSubArea;
use App\EmployeeStore;
use App\Store;
use App\DetailDisplayShare;

trait ExportMTCAdditionalDisplayAchievementTrait
{

	public function CollectDataArea()
	{
		$valueList["Area"] = Employee::where('id_position','6')
		->join('employee_sub_areas','employees.id','employee_sub_areas.id_employee')
                        // ->groupby('employees.id')
		->select('employees.id','employees.name', 'employee_sub_areas.id_subarea as id_sub_area')->get();

		foreach ($valueList["Area"] as $data) {

			$data['store_cover'] = Store::where('id_subarea',$data->id_sub_area)->count();

			$data['store_panel_cover'] = Store::where('id_subarea',$data->id_sub_area)
			->where('stores.store_panel','!=','No')
			->count();

			$data['actual'] = Store::where('stores.id_subarea',$data->id_sub_area)
			->join('additional_displays','stores.id','additional_displays.id_store')
			->whereMonth('additional_displays.date', Carbon::now()->format('m'))
			->whereYear('additional_displays.date', Carbon::now()->format('Y'))
			->groupby('additional_displays.id_store')
			->get()
			->count();

			if ($data['store_panel_cover'] == 0) {
				$data['ach'] = 0;
			}else{
				$data['ach'] = round($data['actual'] / $data['store_panel_cover'] * 100, 2).'%';
			}

			$location = EmployeeSubArea::where('employee_sub_areas.id_employee',$data->id)
			->join('sub_areas','employee_sub_areas.id_subarea','sub_areas.id')
			->pluck('sub_areas.name')->toArray();
			$data['location'] = implode(", ",$location);

		}

		return $valueList["Area"];
	}

	public function CollectDataSPG()
	{
		$valueList["SPG"] = Employee::where('id_position','1')
                        // ->rightjoin('employee_stores','employees.id','employee_stores.id_employee')
                        // ->groupby('employees.id')
                        ->select('employees.id','employees.name')->get();
        foreach ($valueList["SPG"] as $data) {

            $data['store_cover'] = EmployeeStore::where('id_employee',$data->id)->count();

            $data['store_panel_cover'] = EmployeeStore::where('id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $data['actual'] = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('additional_displays','employee_stores.id_store','additional_displays.id_store')
                                ->whereMonth('additional_displays.date', Carbon::now()->format('m'))
                                ->whereYear('additional_displays.date', Carbon::now()->format('Y'))
                                ->groupby('additional_displays.id_store')
                                ->get()
                                ->count();

            if ($data['store_panel_cover'] == 0) {
                    $data['ach'] = 0;
            }else{
                $data['ach'] = round($data['actual'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->pluck('stores.name1')->toArray();
            $data['location'] = implode(", ",$location);
        }

		return $valueList["SPG"];
	}

	public function CollectDataMD()
	{
		$valueList["MD"] = Employee::where('id_position','2')
                        // ->rightjoin('employee_stores','employees.id','employee_stores.id_employee')
                        // ->groupby('employees.id')
                        ->select('employees.id','employees.name')->get();

        foreach ($valueList["MD"] as $data) {

            $data['store_cover'] = EmployeeStore::where('id_employee',$data->id)->count();

            $data['store_panel_cover'] = EmployeeStore::where('id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->where('stores.store_panel','!=','No')
                                ->count();

            $data['actual'] = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('additional_displays','employee_stores.id_store','additional_displays.id_store')
                                ->whereMonth('additional_displays.date', Carbon::now()->format('m'))
                                ->whereYear('additional_displays.date', Carbon::now()->format('Y'))
                                ->groupby('additional_displays.id_store')
                                ->get()
                                ->count();

            if ($data['store_panel_cover'] == 0) {
                    $data['ach'] = 0;
            }else{
                $data['ach'] = round($data['actual'] / $data['store_panel_cover'] * 100, 2).'%';
            }

            $location = EmployeeStore::where('employee_stores.id_employee',$data->id)
                                ->join('stores','employee_stores.id_store','stores.id')
                                ->count() .' STORE';
            $data['location'] = $location;
        }

		return $valueList["MD"];
	}

	public function MTCAdditionalDisplayAchievementExportTrait($limitArea, $limitSpg, $limitMD, $filecode)
	{
		$headerList = [
			[
				["PERFORMANCE AREA/TL", "PERFORMANCE SPG", "PERFORMANCE MD"],
				["AREA", "NAMA STORE", "JML STORE"],
			],
			["JML. STORE COVERAGE", "JLM. STORE PANEL", "ADDITIONAL DISPLAY", ""],
			["ACTUAL", "% ACH."]
		];

		/**
		 * XLS create
		 */
		$filename = "MTC Additional Display Achievement - ".Carbon::now()->format("F Y")." (".$filecode.")";
		$XLS = Excel::create($filename, function($excel) use ($headerList){
			$excel->setTitle("MTC Additional Display - ".Carbon::now()->format("F Y"));
			$excel->setCreator("SADA Technologies");
			$excel->setCompany("SADA Technologies");
			$excel->setLastModifiedBy("SADA Technologies");

			$excel->sheet("Data", function($sheet) use ($headerList){

				$dtObj = [];

                // skip row 1
				$sheet->row(1, function($row){
					$row->setFontSize(11);
				});

                // header row 2 & 3
				$headerList1 = array_merge([$headerList[0][0][0]], $headerList[1]);
				$headerList1 = array_merge($headerList1, [$headerList[0][1][0]]);
				$sheet->mergeCells("D2:E2");
                // $sheet->mergeCells("F2:G2");

				$dtObj["headerWidth1"] = "@";
				for ($i=0; $i < count($headerList1); $i++)
				{
					$dtObj["headerWidth1"] = chr(ord($dtObj["headerWidth1"]) + 1);
					$dtObj["headerWidth1Obj"][] = $dtObj["headerWidth1"];
					$dtObj["regCell"][] = $dtObj["headerWidth1"]."2";

					$sheet->setWidth($dtObj["headerWidth1"], 25);
					if ($dtObj["headerWidth1"] !== "D" && $dtObj["headerWidth1"] !== "E")
					{
						$sheet->mergeCells($dtObj["headerWidth1"]."2:".$dtObj["headerWidth1"]."3");
						$sheet->setWidth($dtObj["headerWidth1"], 25);
					}

				}

				$sheet->row(2, $headerList1);
				$sheet->row(2, function($row){
					$row->setAlignment("center");
					$row->setValignment("center");
					$row->setFontSize(12);
					$row->setFontWeight('bold');
				});

				$startHeader1Char = "D";
				foreach ($headerList[2] as $headerItem)
				{
					$sheet->cell($startHeader1Char."3", function($cell) use ($headerItem){
						$cell->setValue($headerItem);
						$cell->setAlignment("center");
						$cell->setValignment("center");
						$cell->setFontSize(12);
						$cell->setFontWeight('bold');
					});
					$dtObj["regCell"][] = $startHeader1Char."3";
					$startHeader1Char = chr(ord($startHeader1Char) + 1);
				}

                // set value area
				$startRowArea = 4;
				$startRowAreaChar = "A";
				foreach ($this->CollectDataArea() as $dataArea)
				{
					$sheet->row($startRowArea, [
						$dataArea->name,
						$dataArea->store_cover,
						$dataArea->store_panel_cover,
						$dataArea->actual,
						$dataArea->ach,
						$dataArea->location,
					]);
					foreach ($dtObj["headerWidth1Obj"] as $CharAreaData)
					{
						$dtObj["regCell"][] = $CharAreaData . $startRowArea;
					}
					$startRowArea++;
				}

                /**
                 * Skip row
                 */
                $sheet->row(($startRowArea + 1), function($row){
                	$row->setFontSize(11);
                });

				// header row 2 & 3
                $headerList2 = array_merge([$headerList[0][0][1]], $headerList[1]);
                $headerList2 = array_merge($headerList2, [$headerList[0][1][1]]);
                $sheet->mergeCells("D".($startRowArea + 2).":E".($startRowArea + 2)."");
                // $sheet->mergeCells("F".($startRowArea + 2).":G".($startRowArea + 2)."");

                $dtObj["headerWidth2"] = "@";
                for ($i=0; $i < count($headerList2); $i++)
                {
                	$dtObj["headerWidth2"] = chr(ord($dtObj["headerWidth2"]) + 1);
                	$dtObj["headerWidth2Obj"][] = $dtObj["headerWidth2"];
                	$dtObj["regCell"][] = $dtObj["headerWidth2"].($startRowArea + 2);

                	$sheet->setWidth($dtObj["headerWidth2"], 25);
                	if ($dtObj["headerWidth2"] !== "D" && $dtObj["headerWidth2"] !== "E" && $dtObj["headerWidth2"] !== "F" && $dtObj["headerWidth2"] !== "G")
                	{
                		$sheet->mergeCells($dtObj["headerWidth2"].($startRowArea + 2).":".$dtObj["headerWidth2"].($startRowArea + 3));
                		$sheet->setWidth($dtObj["headerWidth2"], 25);
                	}

                }

                $sheet->row(($startRowArea + 2), $headerList2);
                $sheet->row(($startRowArea + 2), function($row){
                	$row->setAlignment("center");
                	$row->setValignment("center");
                	$row->setFontSize(12);
                	$row->setFontWeight('bold');
                });

                $startHeader2Char = "D";
                foreach ($headerList[2] as $headerItem)
                {
                	$sheet->cell($startHeader2Char.($startRowArea + 3), function($cell) use ($headerItem){
                		$cell->setValue($headerItem);
                		$cell->setAlignment("center");
                		$cell->setValignment("center");
                		$cell->setFontSize(12);
                		$cell->setFontWeight('bold');
                	});
                	$dtObj["regCell"][] = $startHeader2Char.($startRowArea + 3);
                	$startHeader2Char = chr(ord($startHeader2Char) + 1);
                }

                // set value spg
                $startRowAreaNext = ($startRowArea + 4);
                $startRowAreaNextChar = "A";
                foreach ($this->CollectDataSPG() as $dataSPG)
                {
                	$sheet->row($startRowAreaNext, [
                		$dataSPG->name,
                		$dataSPG->store_cover,
                		$dataSPG->store_panel_cover,
						$dataSPG->actual,
						$dataSPG->ach,
                		$dataSPG->location,
                	]);
                	foreach ($dtObj["headerWidth2Obj"] as $CharSPGData)
                	{
                		$dtObj["regCell"][] = $CharSPGData . $startRowAreaNext;
                	}
                	$startRowAreaNext++;
                }

                /**
                 * Skip row
                 */
                $sheet->row(($startRowAreaNext + 1), function($row){
                	$row->setFontSize(11);
                });

				// header row 2 & 3
                $headerList3 = array_merge([$headerList[0][0][2]], $headerList[1]);
                $headerList3 = array_merge($headerList3, [$headerList[0][1][2]]);
                $sheet->mergeCells("D".($startRowAreaNext + 2).":E".($startRowAreaNext + 2)."");
                // $sheet->mergeCells("F".($startRowAreaNext + 2).":G".($startRowAreaNext + 2)."");

                $dtObj["headerWidth3"] = "@";
                for ($i=0; $i < count($headerList3); $i++)
                {
                	$dtObj["headerWidth3"] = chr(ord($dtObj["headerWidth3"]) + 1);
                	$dtObj["headerWidth3Obj"][] = $dtObj["headerWidth3"];
                	$dtObj["regCell"][] = $dtObj["headerWidth3"].($startRowAreaNext + 2);

                	$sheet->setWidth($dtObj["headerWidth3"], 25);
                	if ($dtObj["headerWidth3"] !== "D" && $dtObj["headerWidth3"] !== "E" && $dtObj["headerWidth3"] !== "F" && $dtObj["headerWidth3"] !== "G")
                	{
                		$sheet->mergeCells($dtObj["headerWidth3"].($startRowAreaNext + 2).":".$dtObj["headerWidth3"].($startRowAreaNext + 3));
                		$sheet->setWidth($dtObj["headerWidth3"], 25);
                	}

                }

                $sheet->row(($startRowAreaNext + 2), $headerList3);
                $sheet->row(($startRowAreaNext + 2), function($row){
                	$row->setAlignment("center");
                	$row->setValignment("center");
                	$row->setFontSize(12);
                	$row->setFontWeight('bold');
                });

                $startHeader3Char = "D";
                foreach ($headerList[2] as $headerItem)
                {
                	$sheet->cell($startHeader3Char.($startRowAreaNext + 3), function($cell) use ($headerItem){
                		$cell->setValue($headerItem);
                		$cell->setAlignment("center");
                		$cell->setValignment("center");
                		$cell->setFontSize(12);
                		$cell->setFontWeight('bold');
                	});
                	$dtObj["regCell"][] = $startHeader3Char.($startRowAreaNext + 3);
                	$startHeader3Char = chr(ord($startHeader3Char) + 1);
                }

                // dd($dtObj);

                // set value spg
                $startRowAreaNextNext = ($startRowAreaNext + 4);
                $startRowAreaNextNextChar = "A";
                foreach ($this->CollectDataMD() as $dataMD)
                {
                	$sheet->row($startRowAreaNextNext, [
                		$dataMD->name,
                		$dataMD->store_cover,
                		$dataMD->store_panel_cover,
                		$dataMD->actual,
                		$dataMD->ach,
                		$dataMD->location,
                	]);
                	foreach ($dtObj["headerWidth3Obj"] as $CharMDData)
                	{
                		$dtObj["regCell"][] = $CharMDData . $startRowAreaNextNext;
                	}
                	$startRowAreaNextNext++;
                }

                // create border
                foreach($dtObj["regCell"] as $regCellBorder)
                {
                	$sheet->cell($regCellBorder, function($cell){
                		$cell->setBorder("thin", "thin", "thin", "thin");
                	});
                }


            });
})->export("XLSX");


return response()->json($headerList, 200, [], JSON_PRETTY_PRINT);

}

}