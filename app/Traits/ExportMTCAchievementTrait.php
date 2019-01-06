<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\Employee;
use App\EmployeeStore;

trait ExportMTCAchievementTrait
{

	private $headerList = [
		["PERFORMANCE TL", "TAHUN LALU", "BULAN INI", "TARGET", "% ACH", "GROWTH", "ACHIEVEMENT FOKUS 1", "TARGET FOKUS 1", "% ACHIEVEMENT", "ACHIEVEMENT FOKUS 2", "TARGET FOKUS 2", "% ACHIEVEMENT", "AREA"], // TL
		["PERFORMANCE SPG", "TAHUN LALU", "BULAN INI", "TARGET", "% ACH", "GROWTH", "ACHIEVEMENT FOKUS 1", "TARGET FOKUS 1", "% ACHIEVEMENT", "ACHIEVEMENT FOKUS 2", "TARGET FOKUS 2", "% ACHIEVEMENT", "NAMA STORE"], // SPG
		["PERFORMANCE MD", "TAHUN LALU", "BULAN INI", "TARGET", "% ACH", "GROWTH", "ACHIEVEMENT FOKUS 1", "TARGET FOKUS 1", "% ACHIEVEMENT", "ACHIEVEMENT FOKUS 2", "TARGET FOKUS 2", "% ACHIEVEMENT", "JUMLAH STORE"], // MD
	];

	private $valueList = [];
	private $tempVar = [];


	public function MTCAchievementExportTrait($filterPeriode, $filecode)
	{
		$this->tempVar['periode'] = $filterPeriode;
		$this->tempVar['filecode'] = $filecode;

		$this->collectDataTL();
		$this->collectDataSPG();
		$this->collectDataMD();

		return $this->createXLS();
	}

	private function collectDataSPG()
	{
		$periode = Carbon::parse($this->tempVar["periode"]);
		$data = EmployeeStore::whereHas('employee.position', function($query){
		    return $query->where('level', 'spgmtc');
		});
		$data = $data->groupBy(['id_employee','id_store'])->orderBy('id_employee', 'ASC');

		foreach ($data->get() as $spgData)
		{
			$this->valueList["spg"][] = [
				"",
				"",
				$spgData->employee->name,
				number_format($spgData->employee->getActualPrevious(['store' => $spgData->id_store, 'date' => $periode])),
				number_format($spgData->employee->getActual(['store' => $spgData->id_store, 'date' => $periode])),
				number_format($spgData->employee->getTarget(['store' => $spgData->id_store, 'date' => $periode])),
				$spgData->employee->getAchievement(['store' => $spgData->id_store, 'date' => $periode]),
				number_format($spgData->employee->getTarget1Alt(['store' => $spgData->id_store, 'date' => $periode])),
				number_format($spgData->employee->getActualPf1(['store' => $spgData->id_store, 'date' => $periode])),
				$spgData->employee->getAchievementPf1(['store' => $spgData->id_store, 'date' => $periode]),
				number_format($spgData->employee->getTarget2Alt(['store' => $spgData->id_store, 'date' => $periode])),
				number_format($spgData->employee->getActualPf2(['store' => $spgData->id_store, 'date' => $periode])),
				$spgData->employee->getAchievementPf2(['store' => $spgData->id_store, 'date' => $periode]),
				$spgData->employee->getGrowth(['store' => $spgData->id_store, 'date' => $periode]),
				$spgData->store->name1,
			];
		}
	}

	private function collectDataTL()
	{
		$periode = Carbon::parse($this->tempVar["periode"]);
		$data = Employee::with('employeeSubArea.subarea')
		->whereHas('position', function($query){
			return $query->where('level', 'tlmtc');
		});
		$data = $data->orderBy('id', 'ASC');

		foreach ($data->get() as $TLData)
		{
			$this->valueList["tl"][] = [
				"",
				"",
				$TLData->name,
				number_format($TLData->getActualPrevious(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode])),
				number_format($TLData->getActual(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode])),
				number_format($TLData->getTarget(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode])),
				$TLData->getAchievement(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode]),
				number_format($TLData->getTarget1Alt(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode])),
				number_format($TLData->getActualPf1(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode])),
				$TLData->getAchievementPf1(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode]),
				number_format($TLData->getTarget2Alt(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode])),
				number_format($TLData->getActualPf2(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode])),
				$TLData->getAchievementPf2(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode]),
				$TLData->getGrowth(['sub_area' => @$TLData->employeeSubArea[0]->subarea->name, 'date' => $periode]),
				@$TLData->employeeSubArea[0]->subarea->area->name,
			];
		}
	}

	private function collectDataMD()
	{
        $periode = Carbon::parse($this->tempVar["periode"]);
		$data = Employee::whereHas('position', function ($query){
			return $query->where('level', 'mdmtc');
		});
        $data = $data->orderBy('id', 'ASC');

        foreach ($data->get() as $MDData)
        {
        	$this->valueList["md"][] = [
				"",
				"",
				$MDData->name,
				number_format($MDData->getActualPrevious(['date' => $periode])),
				number_format($MDData->getActual(['date' => $periode])),
				number_format($MDData->getTarget(['date' => $periode])),
				$MDData->getAchievement(['date' => $periode]),
				$MDData->getGrowth(['date' => $periode]),
				number_format($MDData->getTarget1Alt(['date' => $periode])),
				number_format($MDData->getActualPf1(['date' => $periode])),
				$MDData->getAchievementPf1(['date' => $periode]),
				number_format($MDData->getTarget2Alt(['date' => $periode])),
				number_format($MDData->getActualPf2(['date' => $periode])),
				$MDData->getAchievementPf2(['date' => $periode]),
				$MDData->employeeStore->count(),
        	];
        }
	}

	public function createXLS()
	{
		$data = [];
        $filename = "MTC Achievement ".Carbon::parse($this->tempVar["periode"])->format("F Y")." (".$this->tempVar["filecode"].")";

        $store = Excel::create($filename, function($excel) use (&$data){
        	$excel->setTitle("MTC Achievement ".Carbon::parse($this->tempVar["periode"])->format("F Y"));

			$excel->setCreator("SADA Technologies");
			$excel->setCompany("SADA Technologies");
			$excel->setLastModifiedBy("SADA Technologies");

        	$excel->sheet("Achievement", function($sheet) use (&$data){

        		// count all width
        		$data["allObjWidth"] = "@";
        		for ($i=0; $i < count($this->headerList[0]); $i++)
        		{
        			$data["allObjWidth"] = chr(ord($data["allObjWidth"]) + 1);
        			$data["allObjWidthList"][] = $data["allObjWidth"];
        		}

        		// set width all row
        		foreach ($data["allObjWidthList"] as $CharKey)
        		{
        			$sheet->setWidth($CharKey, 15);

        			if ($CharKey == "A" || $CharKey == "B")
        				$sheet->setWidth($CharKey, 5);

        			if ($CharKey == "J" || $CharKey == "K" || $CharKey == "M" || $CharKey == "N")
        				$sheet->setWidth($CharKey, 20);

        			if ($CharKey == "C" || $CharKey == "I" || $CharKey == "L" || $CharKey == "O")
        				$sheet->setWidth($CharKey, 25);

        		}

        		// row 1
                $sheet->row(1, function($row){
                    $row->setFontSize(11);
                });

                // row 2
        		// create header title
        		$sheet->mergeCells("C2:" . chr(ord("B") + count($this->headerList[0])) . "2");
                $sheet->row(2, function($row){
                    $row->setFontSize(18);
                });
                $sheet->cell("C2", function($cell){
                	$cell->setValue("EVALUASI PENCAPAIAN SALES MTC");
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                    $cell->setFontWeight('bold');
                    $cell->setBorder("thin", "thin", "thin", "thin");
                });

        		// row 3
                $sheet->row(3, function($row){
                    $row->setFontSize(11);
                });

        		// row 4
        		$this->headerList[0] = array_merge(["", "A."], $this->headerList[0]);
        		$sheet->row(4, $this->headerList[0]);
                $sheet->row(4, function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontSize(12);
                });

        		// row 5++
        		$startTLRow = 5;
        		foreach ($this->valueList["tl"] as $valueTLData) {
	        		$sheet->row($startTLRow, $valueTLData);
	                $sheet->row($startTLRow, function($row){
	                    $row->setFontSize(12);
	                });
	                $startTLRow++;
        		}

        		// row 
                $sheet->row(($startTLRow), function($row){
                    $row->setFontSize(11);
                });

                // row 
        		$this->headerList[1] = array_merge(["", "B."], $this->headerList[1]);
        		$sheet->row(($startTLRow + 1), $this->headerList[1]);
                $sheet->row(($startTLRow + 1), function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontSize(12);
                });

        		// row
        		$startSPGRow = ($startTLRow + 2);
        		foreach ($this->valueList["spg"] as $valueSPGData) {
	        		$sheet->row($startSPGRow, $valueSPGData);
	                $sheet->row($startSPGRow, function($row){
	                    $row->setFontSize(12);
	                });
	                $startSPGRow++;
        		}

        		// row 
                $sheet->row(($startSPGRow), function($row){
                    $row->setFontSize(11);
                });

                // row 
        		$this->headerList[2] = array_merge(["", "C."], $this->headerList[2]);
        		$sheet->row(($startSPGRow + 1), $this->headerList[2]);
                $sheet->row(($startSPGRow + 1), function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontSize(12);
                });

        		// row
        		$startMDRow = ($startSPGRow + 2);
        		foreach ($this->valueList["md"] as $valueSPGData) {
	        		$sheet->row($startMDRow, $valueSPGData);
	                $sheet->row($startMDRow, function($row){
	                    $row->setFontSize(12);
	                });
	                $startMDRow++;
        		}

        	});
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
}