<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\Pf;
use App\Model\Extend\TargetKpiMd;

trait ExportSMDReportNewKPITrait
{

	private $headerList = [
		["INFORMATION", "RATA-RATA PERFORMANCE KPI", "BEST KPI"],
		[
			["AREA", "NAMA SMD", "HK TARGET", "HK ACTUAL", "SUM OF CBD", "SUM OF CALL", "SUM OF EC"],
			[], // sum of
			["SUM OF TOTAL VALUE", "SUM OF VALUE PRODUCT FOKUS", "AVERAGE OF CBD", "AVERAGE OF CALL", "AVERAGE OF EC"],
			[], // avg of
			["AVERAGE SALES TOTAL", "AVERAGE VALUE PRODUCT FOKUS", "CBD", "CALL", "EC"],
			[], // sls
			["SALES TOTAL", "SALES VALUE PRODUCT FOKUS", "TOTAL POINT"]
		]
	];

	private $valueList = [];
	private $tempVar = [];

	public function SMDReportNewKPIExportTrait($filterPeriode, $filterArea, $filecode)
	{
		$this->tempVar['periode'] = $filterPeriode;
		$this->tempVar['area'] = $filterArea;
		$this->tempVar['filecode'] = $filecode;

		$this->createHeader();
		$this->createValue();

		return $this->createXLS();
	}

	private function createHeader()
	{
		$PF = Pf::whereDate('from', '<=', Carbon::parse($this->tempVar['periode']))
        ->whereDate('to', '>=', Carbon::parse($this->tempVar['periode']))
        ->first();

        $this->headerList[1][1][] = "SUM OF " . (isset($PF->category1->name) ? $PF->category1->name : "-");
        $this->headerList[1][3][] = "AVG OF " . (isset($PF->category1->name) ? $PF->category1->name : "-");
        $this->headerList[1][5][] = "BEST OF " . (isset($PF->category1->name) ? $PF->category1->name : "-");

        $this->headerList[1][1][] = "SUM OF " . (isset($PF->category2->name) ? $PF->category2->name : "-");
        $this->headerList[1][3][] = "AVG OF " . (isset($PF->category2->name) ? $PF->category2->name : "-");
        $this->headerList[1][5][] = "BEST OF " . (isset($PF->category2->name) ? $PF->category2->name : "-");

		// merge header list 1
		foreach ($this->headerList[1] as $headerLists)
		{
			foreach ($headerLists as $headerList)
			{
    			$tempHeader[] = $headerList;
			}
		}
		$this->headerList[] = $tempHeader;
	}

	private function createValue()
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

        foreach ($TargetKPI->get() as $KPIData)
        {
        	$this->valueList[] = [
        		$KPIData->area,
        		$KPIData->name,
        		is_null($KPIData->getTarget($this->tempVar['periode'])) ? 0 : $KPIData->getTarget($this->tempVar['periode'])['hk'],
        		$KPIData->getHkActual($this->tempVar['periode']),
				$KPIData->getCbd($this->tempVar['periode']),
				$KPIData->getCall($this->tempVar['periode']),
				$KPIData->getEc($this->tempVar['periode']),
				@$KPIData->getSumCat1($this->tempVar['periode']),
				@$KPIData->getSumCat2($this->tempVar['periode']),
				number_format($KPIData->getTotalValue($this->tempVar['periode'])),
				number_format($KPIData->getSalesValue($this->tempVar['periode'])),
				round($KPIData->getAvgCbd($this->tempVar['periode'])),
				round($KPIData->getAvgCall($this->tempVar['periode'])),
				round($KPIData->getAvgEc($this->tempVar['periode'])),
				@$KPIData->getAvgCat1($this->tempVar['periode']),
				@$KPIData->getAvgCat2($this->tempVar['periode']),
				number_format($KPIData->getAvgTotalValue($this->tempVar['periode'])),
				number_format($KPIData->getAvgSalesValue($this->tempVar['periode'])),
				$KPIData->getBestCbd($this->tempVar['periode']),
				$KPIData->getBestCall($this->tempVar['periode']),
				$KPIData->getBestEc($this->tempVar['periode']),
				@$KPIData->getBestCat1($this->tempVar['periode']),
				@$KPIData->getBestCat2($this->tempVar['periode']),
				$KPIData->getBestTotalValue($this->tempVar['periode']),
				$KPIData->getBestSalesValue($this->tempVar['periode']),
				$KPIData->getTotalPoint($this->tempVar['periode']),
        	];
        }
	}

	private function createXLS()
	{
		$data = [];
        $filename = "SMD Pasar - Report KPI ".Carbon::parse($this->tempVar["periode"])->format("F Y")." (".$this->tempVar["filecode"].")";

        $store = Excel::create($filename, function($excel) use (&$data){
        	$excel->setTitle("SMD Pasar - Report KPI ".Carbon::parse($this->tempVar["periode"])->format("F Y"));

			$excel->setCreator("SADA Technologies");
			$excel->setCompany("SADA Technologies");
			$excel->setLastModifiedBy("SADA Technologies");

        	$excel->sheet("KPI", function($sheet) use (&$data){

        		// count all width
        		$data["allObjWidth"] = "@";
        		for ($i=0; $i < count($this->headerList[2]); $i++)
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

        			if ($CharKey == "J" || $CharKey == "L" || $CharKey == "Q")
        				$sheet->setWidth($CharKey, 20);

        			if ($CharKey == "K" || $CharKey == "R" || $CharKey == "Y")
        				$sheet->setWidth($CharKey, 28);
        		}

        		// set height
        		$sheet->setHeight(4, 20);
        		$sheet->setHeight(5, 30);

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
        				$cell->setValue("KPI SMD Periode " . Carbon::parse($this->tempVar["periode"])->format("F Y"));
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
        			$sheet->mergeCells("A4:K4");
        			$sheet->cell("A4", function($cell){
        				$cell->setValue($this->headerList[0][0]);
        			});
        			$sheet->mergeCells("L4:R4");
        			$sheet->cell("L4", function($cell){
        				$cell->setValue($this->headerList[0][1]);
        			});
        			$sheet->mergeCells("S4:Z4");
        			$sheet->cell("S4", function($cell){
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
	                $sheet->row(5, $this->headerList[2]);
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