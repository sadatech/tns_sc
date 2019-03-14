<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\Cbd;
use App\NewCbd;

trait ExportGTCCbdTrait
{

	private $headerList = [
		["EMPLOYEE", "OUTLET", "AREA", "DATE", "PHOTO"], // old
		["EMPLOYEE", "OUTLET", "AREA", "DATE", "PHOTO", "TOTAL HANGER", "OUTLET TYPE", "CBD POSITION", "CBD COMPETITOR", "POSM"], // new
	];

	private $valueList 	= [];
	private $photoList 	= [];
	private $tempVar 	= [];

	public function GTCCbdExportTrait($filters, $filecode)
	{
		$this->tempVar['filters'] 	= $filters;
		$this->tempVar['filecode'] 	= $filecode;

		$this->collectData();

		return $this->createXLS();
	}

	private function collectData()
	{
		if ($this->tempVar['filters']['new'] != '') {
			$data = NewCbd::query();
		}else{
			$data = Cbd::query();
		}
		
		$data->whereMonth('date',$this->tempVar['filters']['month'])
		->whereYear('date',$this->tempVar['filters']['year'])
		->when($this->tempVar['filters']['day'] != 'null', function($q)
		{
			return $q->whereDay('date',$this->tempVar['filters']['day']);
		})
		->when($this->tempVar['filters']['employee'] != 'null', function($q)
		{
			return $q->whereIdEmployee($this->tempVar['filters']['employee']);
		})
		->when($this->tempVar['filters']['outlet'] != 'null', function($q)
		{
			return $q->whereIdOutlet($this->tempVar['filters']['outlet']);
		})
        ->when($this->tempVar['filters']['area'] != 'null', function($q){
            $q->whereHas('outlet.employeePasar.pasar.subarea.area', function($q2){
                return $q2->where('id_area', $this->tempVar['filters']['area']);
            });
        })
        ;

		foreach ($data->get() as $a => $d)
		{
			if ($this->tempVar['filters']['new'] != '') {
				$this->valueList[$a] = [
					$d->employee->name,
					$d->outlet->name,
					$d->outlet->employeePasar->pasar->subarea->area->name,
					$d->date,
					"",
					$d->total_hanger, 
					$d->outlet_type, 
					$d->cbd_position,
					$d->cbd_competitor,
					$d->posm,
				];

				$this->photoList[$a] = $d->photo;

			}else{
				$this->valueList[$a] = [
					$d->employee->name,
					$d->outlet->name,
					$d->outlet->employeePasar->pasar->subarea->area->name,
					$d->date,
				];

				$this->photoList[$a] = $d->photo;
			}
		}
	}

	public function createXLS()
	{
		$label = '';
		if ($this->tempVar['filters']['new'] != '') {
			$index = 1;
			$label = "NEW ";
		}else{
			$index = 0;
		}

		$data = [];
		$filename = "GTC ".$label."CBD ".Carbon::parse("1/".$this->tempVar['filters']['month'].'/'.$this->tempVar['filters']['year'])->format("F Y")." (".$this->tempVar["filecode"].")";


		$store = Excel::create($filename, function($excel) use (&$data, $index, $label){
			$excel->setTitle("GTC ".$label."CBD ".Carbon::parse("1/".$this->tempVar['filters']['month'].'/'.$this->tempVar['filters']['year'])->format("F Y"));

			$excel->setCreator("SADA Technologies");
			$excel->setCompany("SADA Technologies");
			$excel->setLastModifiedBy("SADA Technologies");

			$excel->sheet("Data", function($sheet) use (&$data, $index){


        		// count all width
				$data["allObjWidth"] = "@";
				for ($i=0; $i < count($this->headerList[$index]); $i++)
				{
					$data["allObjWidth"] = chr(ord($data["allObjWidth"]) + 1);
					$data["allObjWidthList"][] = $data["allObjWidth"];
				}

        		// count all height
        		$data["allObjHeight"] = 1;
        		$data["allObjHeightList"] = [1];
        		for ($i=0; $i < count($this->valueList); $i++)
        		{
        			$data["allObjHeight"] = (int) chr(ord($data["allObjHeight"]) + 1);
        			$data["allObjHeightList"][] = $data["allObjHeight"];
        		}

        		// row 1
				$sheet->row(1, $this->headerList[$index]);
				$sheet->row(1, function($row){
					$row->setAlignment("center");
					$row->setValignment("center");
					$row->setFontWeight('bold');
					$row->setFontSize(12);
				});

        		// row 2++
				$startTLRow = 2;
				foreach ($this->valueList as $valueTLKey => $valueTLData) {
					$sheet->row($startTLRow, $valueTLData);
					$sheet->row($startTLRow, function($row){
						$row->setFontSize(12);
					});

	            	$imgDrawing = new PHPExcel_Worksheet_Drawing;
	            	if (isset($this->photoList[$valueTLKey]))
	            	{
	            		if (file_exists(public_path("/uploads/cbd/".($this->photoList[$valueTLKey]))))
	            		{
		            		$imgDrawing->setPath(public_path("/uploads/cbd/".($this->photoList[$valueTLKey])));
		            		$imgDrawing->setCoordinates("E".($startTLRow));
		            		$imgDrawing->setWorksheet($sheet);
		            		$imgDrawing->setWidth(40);
	            		}else{
		            		$sheet->setCellValue('E'.$startTLRow, "not found")->setAutoSize(true);
	            		}
	            	}

					$startTLRow++;
				}

				// create all border
				foreach ($data["allObjWidthList"] as $__allWidth)
				{
					foreach ($data["allObjHeightList"] as $__allHeight)
					{
						$sheet->cell($__allWidth.($__allHeight), function($cell){
							$cell->setBorder("thin", "thin", "thin", "thin");
						});
					}
				}

			});
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
}