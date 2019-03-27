<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\Pf;
use App\Model\Extend\TargetKpiMd;

trait ExportSMDReportTargetKPITrait
{

	private $headerList = [
		["INFORMATION", "TARGET/BULAN", "ACHIEVEMENT"],
		[
			["NO", "NAMA SMD", "AREA", "HK TARGET", "SALES VALUE", "EC PRODUK FOKUS", "CBD", "SALES VALUE", "EC PRODUK FOKUS", "CBD"]
		]
	];

	private $valueList = [];
	private $tempVar = [];

	public function SMDReportTargetKPIExportTrait($filterPeriode, $filterArea, $filecode)
	{
        $this->tempVar['periode'] = $filterPeriode;
		$this->tempVar['area'] = $filterArea;
		$this->tempVar['filecode'] = $filecode;

		$this->createValue();

		return $this->createXLS();
	}

	public function createValue()
	{
		$TargetKPI = TargetKpiMd::where('isResign', 0)->whereHas('position', function($query){
            return $query->where('level', 'mdgtc');
        });

        if($this->tempVar['area'] != null && $this->tempVar['area'] != 'null'){
            $TargetKPI = $TargetKPI->join('employee_pasars','employees.id','employee_pasars.id_employee')
                                    ->join('pasars','employee_pasars.id_pasar','pasars.id')
                                    ->join('sub_areas','pasars.id_subarea','sub_areas.id')
                                    ->where('sub_areas.id_area', $this->tempVar['area']);
        }

		$startNumber = 1;
        foreach ($TargetKPI->get() as $KPIData)
        {
        	$this->valueList[] = [
        		$startNumber,
        		$KPIData->name,
        		$KPIData->area,
		        is_null($KPIData->getTarget($this->tempVar['periode'])) ? 0 : $KPIData->getTarget($this->tempVar['periode'])['hk'],
		        number_format(is_null($KPIData->getTarget($this->tempVar['periode'])) ? 0 : $KPIData->getTarget($this->tempVar['periode'])['value_sales']),
		        is_null($KPIData->getTarget($this->tempVar['periode'])) ? 0 : $KPIData->getTarget($this->tempVar['periode'])['ec'],
		        is_null($KPIData->getTarget($this->tempVar['periode'])) ? 0 : $KPIData->getTarget($this->tempVar['periode'])['cbd'],
		        number_format(@$KPIData->getSalesValue($this->tempVar['periode'])),
		        number_format(@$KPIData->getEc($this->tempVar['periode'])),
		        number_format(@$KPIData->getCbd($this->tempVar['periode'])),
        	];
        	$startNumber++;
        }
	}

	public function createXLS()
	{
		$data = [];
        $filename = "SMD Pasar - Report Target KPI ".Carbon::parse($this->tempVar["periode"])->format("F Y")." (".$this->tempVar["filecode"].")";

        $store = Excel::create($filename, function($excel) use (&$data){
        	$excel->setTitle("SMD Pasar - Report Target KPI ".Carbon::parse($this->tempVar["periode"])->format("F Y"));

			$excel->setCreator("SADA Technologies");
			$excel->setCompany("SADA Technologies");
			$excel->setLastModifiedBy("SADA Technologies");

        	$excel->sheet("Target KPI", function($sheet) use (&$data){

        		// count all width
        		$data["allObjWidth"] = "@";
        		for ($i=0; $i < count($this->headerList[1][0]); $i++)
        		{
        			$data["allObjWidth"] = chr(ord($data["allObjWidth"]) + 1);
        			$data["allObjWidthList"][] = $data["allObjWidth"];
        		}

        		// count all height
        		$data["allObjHeight"] = 6;
        		$data["allObjHeightList"] = [1,2,3,4,5,6];
        		for ($i=0; $i < count($this->valueList); $i++)
        		{
        			$data["allObjHeight"] = (int) chr(ord($data["allObjHeight"]) + 1);
        			$data["allObjHeightList"][] = $data["allObjHeight"];
        		}

        		// set width all row
        		foreach ($data["allObjWidthList"] as $CharKey)
        		{
        			$sheet->setWidth($CharKey, 15);

        			if ($CharKey == "A")
        				$sheet->setWidth($CharKey, 5);

        		}

        		// set height
        		$sheet->setHeight(4, 20);
        		$sheet->setHeight(5, 30);

        		// freeze
        		$sheet->setFreeze("D5");

        		/**
        		 * Create row
        		 */
        		if (time())
        		{

        			/**
        			 * row 1
        			 */
	                $sheet->row(1, function($row){
	                    $row->setFontSize(10);
	                });

	                /**
	                 * row 2
	                 */
        			$sheet->mergeCells( $data["allObjWidthList"][0] . "2:" . $data["allObjWidthList"][5] . "2");
        			$sheet->cell($data["allObjWidthList"][0] . "2", function($cell){
        				$cell->setValue("Target KPI SMD Periode " . Carbon::parse($this->tempVar["periode"])->format("F Y"));
        				$cell->setFontSize(14);
        			});

        			/**
        			 * row 3
        			 */
	                $sheet->row(3, function($row){
	                    $row->setFontSize(10);
	                });

        			/**
        			 * row 4
        			 */
        			$sheet->mergeCells("A4:D4");
        			$sheet->cell("A4", function($cell){
        				$cell->setValue($this->headerList[0][0]);
        			});
        			$sheet->mergeCells("E4:G4");
        			$sheet->cell("E4", function($cell){
        				$cell->setValue($this->headerList[0][1]);
        			});
        			$sheet->mergeCells("H4:J4");
        			$sheet->cell("H4", function($cell){
        				$cell->setValue($this->headerList[0][2]);
        			});
	                $sheet->row(4, function($row){
	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                    $row->setFontWeight('bold');
	                    $row->setFontSize(10);
	                });

        			/**
        			 * row 5
        			 */
	                $sheet->row(5, $this->headerList[1][0]);
	                $sheet->row(5, function($row){
	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                    $row->setFontWeight('bold');
	                    $row->setFontSize(10);
	                });

        			/**
        			 * row 6+
        			 */
        			$rowValueStart = 6;
        			foreach ($this->valueList as $ValueData)
        			{
        				$sheet->row($rowValueStart, $ValueData);
        				$rowValueStart++;
        			}

        		}

    			// $newHeightValue = ($data["allObjHeight"] + 1);
    			// $data["allObjHeight"] = $newHeightValue;
    			// $data["allObjHeightList"][] = $data["allObjHeight"];

	            // create all border
	            foreach ($data["allObjWidthList"] as $__allWidth)
	            {
	                foreach ($data["allObjHeightList"] as $__allHeight)
	                {
	                	if ($__allHeight > 3)
	                	{
		                    $sheet->cell($__allWidth.($__allHeight), function($cell){
		                        $cell->setBorder("thin", "thin", "thin", "thin");
		                    });
	                	}
	                }
	            }

        	});
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
}