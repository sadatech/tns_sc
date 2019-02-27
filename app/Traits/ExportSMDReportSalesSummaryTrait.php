<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;

use App\ProductFokusGtc;
use App\SubCategory;
use App\StockMdDetail;
use App\Product;
use App\Model\Extend\SalesMdSummary;

trait ExportSMDReportSalesSummaryTrait
{

	private $headerList = [
		["INFORMATION", "DISTRIBUSI PRODUK FOKUS", "SALES [ UNIT ] / PACK", "SUMMARY", "OOS (STOKIES)"],
		[
			["AREA", "NAMA SMD", "JABATAN", "NAMA PASAR", "NAMA STOKIST", "BULAN", "TANGGAL", "CALL", "RO"],
			[],
			[],
			["EC", "VALUE PRODUCT FOKUS", "VALUE NON PRODUK FOKUS", "VALUE TOTAL", "CBD"],
			[],
			["KET"]
		]
	];

	private $value_list = [];
	private $tempData = [];

	public function SMDReportSalesSummaryExportTrait($filterPeriode, $filterArea, $filecode)
	{
		$periode = Carbon::parse($filterPeriode)->format('Y-m-d');

		$id_subcategories = array_unique(ProductFokusGtc::whereDate('from', '<=', $periode)->whereDate('to', '>=', $periode)->get()->pluck('product.subcategory.id')->toArray());
		$subcategories = SubCategory::whereIn('id', $id_subcategories)->get();

		foreach ($subcategories as $subcategory)
		{
			$this->headerList[1][1][] = strtoupper("Dist. " . $subcategory->name);
			$this->headerList[1][2][] = strtoupper("Sales " . $subcategory->name);
		}

		$id_product_oos = StockMdDetail::whereHas('stock', function ($query) use ($periode){
			return $query->whereMonth('date', Carbon::parse($periode)->month)->whereYear('date', Carbon::parse($periode)->year);
        })->pluck('id_product')->toArray();

        $products = Product::whereIn('id', $id_product_oos)->get();

        foreach ($products as $product)
        {
        	$this->headerList[1][4][] = strtoupper($product->name);
        }

		// merge header list 1
		foreach ($this->headerList[1] as $headerLists)
		{
			foreach ($headerLists as $headerList)
			{
    			$tempHeader[] = $headerList;
			}
		}
		$this->headerList[3] = $tempHeader;

        /**
         * 
         */
        $sales = SalesMdSummary::whereMonth('date', Carbon::parse($filterPeriode)->month)
		->whereYear('date', Carbon::parse($filterPeriode)->year)
		->groupBy('id_employee', 'date')
		->orderBy('date', 'DESC')
		->orderBy('id_employee', 'ASC');

        if($filterArea != null && $filterArea != 'null'){
            $sales->whereHas('outlet.employeePasar.pasar.subarea.area', function($q) use ($filterArea){
                return $q->where('id_area', $filterArea);
            }); 
        }
        
		foreach ($sales->get() as $salesData)
		{
			$csm = [
				$salesData->area,
				$salesData->nama_smd,
				$salesData->jabatan,
				$salesData->nama_pasar,
				$salesData->nama_stokist,
				Carbon::parse($salesData->tanggal)->format('F'),
				(int) Carbon::parse($salesData->tanggal)->format('d'),
				$salesData->call,
				$salesData->ro,
			];

			foreach ($salesData->distribusi_pf as $salesDataDistPF)
			{
				$csm[] = $salesDataDistPF;
			}

			foreach ($salesData->sales_pf as $salesDataSalesPF)
			{
				$csm[] = $salesDataSalesPF;
			}

			$csm[] = $salesData->eff_call;
			$csm[] = number_format($salesData->value_pf);
			$csm[] = number_format($salesData->value_non_pf);
			$csm[] = number_format($salesData->value_total);

			$csm[] = $salesData->cbd;

			foreach ($salesData->oos as $salesDataOOS)
			{
				$csm[] = $salesDataOOS;
			}

			$this->value_list[] = $csm;
		}

		return $this->makeXLSX($filterPeriode, $filterArea, $filecode);
	}


	private function makeXLSX($filterPeriode, $filterArea, $filecode)
	{
		$data = [];

        $filename = "SMD Pasar - Report Sales Summary ".Carbon::parse($filterPeriode)->format("F Y")." (".$filecode.")";
        $store = Excel::create($filename, function($excel) use ($filterPeriode, &$data){

        	$excel->setTitle("SMD Pasar - Report Sales Summary ".Carbon::parse($filterPeriode)->format("F Y"));

			$excel->setCreator("SADA Technologies");
			$excel->setCompany("SADA Technologies");

        	$excel->sheet("Raw Data", function($sheet) use (&$data){

        		// count all width
        		$data["allObjWidth"] = "@";
        		for ($i=0; $i < count($this->headerList[3]); $i++)
        		{
        			$data["allObjWidth"] = chr(ord($data["allObjWidth"]) + 1);
        			$data["allObjWidthList"][] = $data["allObjWidth"];
        		}

        		// count all height
        		$data["allObjHeight"] = 0;
        		for ($i=0; $i < count($this->value_list); $i++)
        		{
        			$data["allObjHeight"] = chr(ord($data["allObjHeight"]) + 1);
        			$data["allObjHeightList"][] = $data["allObjHeight"];
        		}

        		// set width all row
        		foreach ($data["allObjWidthList"] as $CharKey)
        		{
        			$sheet->setWidth($CharKey, 15);
        		}

        		// set height header
        		$sheet->setHeight(4, 25);
        		$sheet->setHeight(5, 20);

        		// freeze header
        		$sheet->setFreeze($data["allObjWidthList"][0 + 9]."1");

        		/**
        		 * Create basic header
        		 */
        		if (time())
        		{
        			/**
        			 * row 1
        			 */
	                $sheet->row(1, function($row){
	                    $row->setFontSize(11);
	                });

	                /**
	                 * row 2
	                 */
        			$sheet->mergeCells( $data["allObjWidthList"][0] . "2:" . $data["allObjWidthList"][5] . "2");
        			$sheet->cell($data["allObjWidthList"][0] . "2", function($cell){
        				$cell->setValue("Laporan Harian Team SMD dan SC Sasa");
        				$cell->setFontSize(14);
        			});

        			/**
        			 * row 3
        			 */
	                $sheet->row(3, function($row){
	                    $row->setFontSize(11);
	                });

        			/**
        			 * row 4
        			 */
        			$sheet->mergeCells( $data["allObjWidthList"][0] . "4:" . $data["allObjWidthList"][(8 + count($this->headerList[1][1]) - 1)] . "4");
        			$sheet->cell($data["allObjWidthList"][0] . "4", function($cell){
        				$cell->setValue($this->headerList[0][0]);
	                    $cell->setAlignment("center");
	                    $cell->setValignment("center");
	                    $cell->setFontWeight('bold');
	                    $cell->setFontSize(12);
        			});
        			$sheet->mergeCells( $data["allObjWidthList"][9] . "4:" . $data["allObjWidthList"][(8 + count($this->headerList[1][1]))] . "4");
        			$sheet->cell($data["allObjWidthList"][9] . "4", function($cell){
        				$cell->setValue($this->headerList[0][1]);
	                    $cell->setAlignment("center");
	                    $cell->setValignment("center");
	                    $cell->setFontWeight('bold');
	                    $cell->setFontSize(12);
        			});
        			$sheet->setWidth($data["allObjWidthList"][9], 30);
        			$sheet->mergeCells( $data["allObjWidthList"][(8 + count($this->headerList[1][1]) + 1)] . "4:" . $data["allObjWidthList"][(8 + count($this->headerList[1][1]) + count($this->headerList[1][2]))] . "4");
        			$sheet->cell($data["allObjWidthList"][(8 + count($this->headerList[1][1]) + 1)] . "4", function($cell){
        				$cell->setValue($this->headerList[0][2]);
 	                    $cell->setAlignment("center");
	                    $cell->setValignment("center");
	                    $cell->setFontWeight('bold');
	                    $cell->setFontSize(12);
	       			});
	       			$sheet->setWidth($data["allObjWidthList"][(8 + count($this->headerList[1][1]) + 1)], 30);
        			$sheet->mergeCells( $data["allObjWidthList"][(8 + count($this->headerList[1][1]) + 1 + 1)] . "4:" . $data["allObjWidthList"][(8 + count($this->headerList[1][1]) + count($this->headerList[1][2]) + 5)] . "4");
        			$sheet->cell($data["allObjWidthList"][(8 + count($this->headerList[1][1]) + 1 + 1)] . "4", function($cell){
        				$cell->setValue($this->headerList[0][3]);
 	                    $cell->setAlignment("center");
	                    $cell->setValignment("center");
	                    $cell->setFontWeight('bold');
	                    $cell->setFontSize(12);
	       			});
	       			$sheet->setWidth($data["allObjWidthList"][12], 30);
	       			$sheet->setWidth($data["allObjWidthList"][13], 30);
	       			$sheet->setWidth($data["allObjWidthList"][14], 30);
        			$sheet->mergeCells( $data["allObjWidthList"][(8 + count($this->headerList[1][1]) + count($this->headerList[1][2]) + 5 + 1)] . "4:" . $data["allObjWidthList"][(8 + count($this->headerList[1][1]) + count($this->headerList[1][2]) + 5 + count($this->headerList[1][4]) + 1)] . "4");
        			$sheet->cell($data["allObjWidthList"][(8 + count($this->headerList[1][1]) + count($this->headerList[1][2]) + 5 + 1)] . "4", function($cell){
        				$cell->setValue($this->headerList[0][4]);
 	                    $cell->setAlignment("center");
	                    $cell->setValignment("center");
	                    $cell->setFontWeight('bold');
	                    $cell->setFontSize(12);
	       			});

	       			$startRow = (8 + count($this->headerList[1][1]) + count($this->headerList[1][2]) + 5);
	       			for ($i=0; $i < count($this->headerList[1][4]); $i++)
	       			{
	       				$startRow++;
	       				$sheet->setWidth($data["allObjWidthList"][$startRow], 30);
	       			}

        			/**
        			 * row 5
        			 */
        			$sheet->row(5, $this->headerList[3]);
        			$sheet->row(5, function($row){
 	                    $row->setAlignment("center");
	                    $row->setValignment("center");
	                    $row->setFontWeight('bold');
	                    $row->setFontSize(12);
        			});

        			/**
        			 * row 6+
        			 */
        			$rowValueStart = 6;
        			foreach ($this->value_list as $ValueData)
        			{
        				$sheet->row($rowValueStart, $ValueData);
        				$rowValueStart++;
        			}

        		}

        		// push list height
        		for ($i=0; $i < count($this->value_list); $i++)
        		{
        			$newHeightValue = ($data["allObjHeight"] + 1);
        			$data["allObjHeight"] = $newHeightValue;
        			$data["allObjHeightList"][] = $data["allObjHeight"];
        		}

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