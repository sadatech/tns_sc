<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\AdditionalDisplay;

use Illuminate\Database\Eloquent\Collection;

trait ExportMTCAdditionalDisplayTrait
{
	protected $request, $AdditionalDisplayData, $filecode;
	
	public function MTCAdditionalDisplayExportTrait($request, $filecode)
	{
		$this->addDisplayData = [];
		$this->request = $request;
		$this->filecode = $filecode;
		
		$this->collectDataRow();
		return $this->createXLS($this->xlsHelper());
	}
	
	private function createXLS($AdditionalDisplayData)
	{
		$filename = 'MTC - Additional Display - '.Carbon::parse('01/'.$this->request['periode'])->format('F').' '.Carbon::parse('01/'.$this->request['periode'])->format('Y') . " (" . $this->filecode . ")";
		$excel = Excel::create($filename, function($excel) use ($AdditionalDisplayData){
			/** Set information */
			$excel->setTitle('MTC - Report Additional Display');
			$excel->setCreator('SADA');
			$excel->setCompany('SADA');
			$excel->setDescription('MTC - Report Additional Display');
			
			/** */
			$excel->getDefaultStyle()
			->getAlignment()
			->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
			->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			/**  */
			$excel->sheet('ADDITIONAL DISPLAY', function ($sheet) use ($AdditionalDisplayData){
				$sheet->setHeight(1, 25);
				$sheet->fromModel($AdditionalDisplayData, null, 'A1', true, true);
				$sheet->row(1, function ($row){
					$row->setBackground('#82abde');
				});
				$sheet->cells('A1:J1', function($cells){
					$cells->setFontWeight('bold');
				});
				$sheet->setBorder('A1:J1', 'thin');
			});
			
			/**  */
		})->store("xlsx", public_path("export/report"), true);
		
		return asset("export/report") . "/" . $filename . ".xlsx";
	}
	
	private function xlsHelper()
	{
		$collection = collect($this->addDisplayData)->take($this->request['limit']);
		return $collection->map(function($item){
			$listData = [
				"REGION" => @$item["region_name"],
				"AREA" => @$item["area_name"],
				"TL" => @$item["tl_name"],
				"JABATAN" => @$item["jabatan"],
				"NAME" => @$item["emp_name"],
				"STORE" => @$item["store_name"],
				"WAKTU" => @$item["date"],
				"JENIS DISPLAY" => @$item["jenis_display_name"],
				"JUMLAH ADD" => @$item["jumlah_add"],
				"FOTO" => @$item["foto_Add"]
			];

			return $listData;
		});
	}
	
	private function collectDataRow()
	{
		$datas = AdditionalDisplay::where('additional_displays.deleted_at', null)
		->join("stores", "additional_displays.id_store", "=", "stores.id")
		->join('sub_areas', 'stores.id_subarea', 'sub_areas.id')
		->join('areas', 'sub_areas.id_area', 'areas.id')
		->join('regions', 'areas.id_region', 'regions.id')
		->leftjoin('employee_sub_areas', 'stores.id', 'employee_sub_areas.id_subarea')
		->leftjoin('employees as empl_tl', 'employee_sub_areas.id_employee', 'empl_tl.id')
		->join("employees", "additional_displays.id_employee", "=", "employees.id")
		->leftjoin("detail_additional_displays", "additional_displays.id", "=", "detail_additional_displays.id_additional_display")
		->join("jenis_displays", "detail_additional_displays.id_jenis_display", "=", "jenis_displays.id")
		->when($this->request['id_employee'], function ($q){
			return $q->where('additional_displays.id_employee', $this->request['id_employee']);
		})
		->when($this->request['periode'], function ($q){
			return $q->whereMonth('date', substr($this->request['periode'], 0, 2))
			->whereYear('date', substr($this->request['periode'], 3));
		})
		->when(!empty($this->request['id_store']), function ($q){
			return $q->where('id_store', $this->request['id_store']);
		})
		->when($this->request['id_area'], function ($q){
			return $q->where('id_area', $this->request['id_area']);
		})
		->select(
			'additional_displays.*',
			'stores.name1 as store_name',
			'employees.name as emp_name',
			'jenis_displays.name as jenis_display_name',
			'detail_additional_displays.jumlah as jumlah_add',
			'detail_additional_displays.foto_additional as foto_Add',
			'regions.name as region_name',
			'areas.name as area_name',
			'empl_tl.name as tl_name',
			'employees.status as jabatan'
		)
		->get();

		$this->addDisplayData = $datas;
	}
}