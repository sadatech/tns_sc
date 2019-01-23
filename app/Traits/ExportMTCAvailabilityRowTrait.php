<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\SubArea;
use App\Store;
use App\Product;
use App\DataPrice;
use App\ProductCompetitor;

use Illuminate\Database\Eloquent\Collection;

trait ExportMTCAvailabilityRowTrait
{
	protected $request, $AvailabilityRowData, $filecode;
	
	public function MTCAvailabilityRowExportTrait($request, $filecode)
	{
		$this->prirceRowData = [];
		$this->request = $request;
		$this->filecode = $filecode;
		
		$this->collectDataRow();
		return $this->createXLS($this->xlsHelper());
	}

	private function createXLS($AvailabilityRowData)
	{
		$filename = 'MTC - AVAILABILITY ROW - '.Carbon::parse('01/'.$this->request['periode'])->format('F').' '.Carbon::parse('01/'.$this->request['periode'])->format('Y') . " (" . $this->filecode . ")";
		$excel = Excel::create($filename, function($excel) use ($AvailabilityRowData){
            /** Set information */
            $excel->setTitle('MTC - Report Availability Row');
            $excel->setCreator('SADA');
            $excel->setCompany('SADA');
            $excel->setDescription('MTC - Report Availability Row');

            /** */
            $excel->getDefaultStyle()
            ->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            /**  */
            $excel->sheet('AVAILABILITY ROW', function ($sheet) use ($AvailabilityRowData){

            });

            /**  */
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
	
	private function xlsHelper()
	{
	}
	
	private function collectDataRow()
	{
	}
	
}