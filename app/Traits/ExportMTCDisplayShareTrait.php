<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;
use PHPExcel_Worksheet_Drawing;

use App\Employee;
use App\Category;
use App\Brand;
use App\DisplayShare;
use App\DetailDisplayShare;

trait ExportMTCDisplayShareTrait
{
	public function MTCDisplayShareExportTrait($periode, $id_employee, $id_store, $id_area, $limit, $filecode)
	{
        /**
         * 
         */
        $categories = Category::get();
        $brands = Brand::get();

        $datas = DisplayShare::where('display_shares.deleted_at', null)
        ->join("stores", "display_shares.id_store", "=", "stores.id")
        ->join('sub_areas', 'stores.id_subarea', 'sub_areas.id')
        ->join('areas', 'sub_areas.id_area', 'areas.id')
        ->join('regions', 'areas.id_region', 'regions.id')
        ->join('accounts', 'stores.id_account', 'accounts.id')
        ->leftjoin('employee_sub_areas', 'stores.id', 'employee_sub_areas.id_subarea')
        ->leftjoin('employees as empl_tl', 'employee_sub_areas.id_employee', 'empl_tl.id')
        ->join("employees", "display_shares.id_employee", "=", "employees.id")
        ->leftjoin("detail_display_shares", "display_shares.id", "=", "detail_display_shares.id_display_share")
        ->groupby('display_shares.id_store')
        ->when($id_employee, function ($q) use ($id_employee){
            return $q->where('display_shares.id_employee', $id_employee);
        })
        ->when(!empty($id_store), function ($q) use ($id_store){
            return $q->where('id_store', $id_store);
        })
        ->when($id_area, function ($q) use ($id_area){
            return $q->where('id_area', $id_area);
        })
        ->select(
            'display_shares.*',
            'stores.name1 as store_name',
            'employees.name as emp_name',
            'regions.name as region_name',
            'areas.name as area_name',
            'empl_tl.name as tl_name',
            'employees.status as jabatan',
            'accounts.name as account_name'
        )
        ->limit($limit)
        ->get();

        foreach ($datas as $key => $data)
        {
            $valueList[$key] = [
                $data->region_name,
                $data->area_name,
                $data->tl_name,
                $data->emp_name,
                $data->jabatan,
                $data->store_name,
                $data->account_name,
            ];

            $tierDataTotal[$key]  = 0;
            $depthDataTotal[$key] = 0;

            foreach ($categories as $category)
            {
                foreach ($brands as $keyBrand => $brand)
                {
                    $detail_data = DetailDisplayShare::where('detail_display_shares.id_display_share', $data->id)
                    ->where('detail_display_shares.id_category',$category->id)
                    ->where('detail_display_shares.id_brand',$brand->id)
                    ->first();

                    $tierDataTotal[$key]  += (isset($detail_data->tier) ? $detail_data->tier : 0);
                    $depthDataTotal[$key] += (isset($detail_data->depth) ? $detail_data->depth : 0);
                    $valueList[$key][]     = (isset($detail_data->tier) ? $detail_data->tier : "-");
                    $valueList[$key][]     = (isset($detail_data->depth) ? $detail_data->depth : "-");
                }

            }
            $valueList[$key][] = $tierDataTotal[$key];
            $valueList[$key][] = $depthDataTotal[$key];
        }

        /**
         * Create Header Excel
         */
        $headerList = [
            ["REGION", "AREA", "TL", "NAMA SPG", "JABATAN", "STORE", "ACCOUNT"],
        ];
        foreach ($categories as $category)
        {
            $headerList[1][] = $category->name;
            foreach ($brands as $brand)
            {
                $headerList[2][] = strtoupper($brand->name);
                $headerList[3][] = ["TIER", "DEPTH"];
            }
        }
        $headerList[2][] = strtoupper("total");
        $headerList[3][] = ["TIER", "DEPTH"];

        /**
         * Create XLS
         */
        $filename = "MTC Display Share - " . (is_null($id_employee) ? "All Employee" : Employee::where("id", $id_employee)->first()->name) . " - ".Carbon::parse("01/".$periode)->format("F Y")." (".$filecode.")";
        $XLS = Excel::create($filename, function($excel) use ($periode, $id_employee, $datas, $headerList, $valueList){
            $excel->setTitle("MTC Display Share - " . (is_null($id_employee) ? "All Employee" : Employee::where("id", $id_employee)->first()->name) . " - ".Carbon::parse("01/".$periode)->format("F Y"));
            $excel->setCreator("SADA Technologies");
            $excel->setCompany("SADA Technologies");
            $excel->setLastModifiedBy("SADA Technologies");

            $excel->sheet("Data", function($sheet) use ($datas, $headerList, $valueList){

                $dtObj["regCell"] = [];

                // count basic header
                $dtObj["headerWidthBasic"] = "@";
                for ($i=0; $i < count($headerList[0]); $i++)
                {
                    $dtObj["headerWidthBasic"] = chr(ord($dtObj["headerWidthBasic"]) + 1);
                    $dtObj["headerWidthBasicObj"][] = $dtObj["headerWidthBasic"];
                    $dtObj["regCell"][] = $dtObj["headerWidthBasic"]."1";
                }

                // merge header basic
                foreach ($dtObj["headerWidthBasicObj"] as $headerWidthBasic)
                {
                    $sheet->mergeCells($headerWidthBasic."1:".$headerWidthBasic."3");
                    $sheet->setWidth($headerWidthBasic, 15);
                }

                // create table hedaer basic
                $sheet->row(1, $headerList[0]);
                $sheet->row(1, function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontSize(12);
                    $row->setFontWeight('bold');
                });

                // create table header category
                $dtObj["headerWidthBasic2"] = chr(ord($dtObj["headerWidthBasic"]) + 1);
                foreach ($headerList[1] as $headerList1)
                {
                    $dtObj["regCell"][] = $dtObj["headerWidthBasic2"]."1";
                    $sheet->mergeCells($dtObj["headerWidthBasic2"]."1:".chr(ord($dtObj["headerWidthBasic2"])+(2 * count($headerList[3]) - 1))."1");
                    $sheet->cell($dtObj["headerWidthBasic2"]."1", $headerList1);
                    $sheet->cell($dtObj["headerWidthBasic2"]."1", function($cell){
                        $cell->setAlignment("center");
                        $cell->setValignment("center");
                        $cell->setFontSize(12);
                        $cell->setFontWeight('bold');
                    });
                    $dtObj["headerWidthBasic2"] = chr(ord($dtObj["headerWidthBasic2"]) + 1);
                }

                // create table header brands
                $dtObj["headerWidthBasic3"] = chr(ord($dtObj["headerWidthBasic"]) + 1);
                foreach ($headerList[2] as $headerList2)
                {
                    $dtObj["regCell"][] = $dtObj["headerWidthBasic3"]."2";
                    $sheet->mergeCells($dtObj["headerWidthBasic3"]."2:".chr(ord($dtObj["headerWidthBasic3"]) + 1)."2");
                    $sheet->cell($dtObj["headerWidthBasic3"]."2", $headerList2);
                    $sheet->cell($dtObj["headerWidthBasic3"]."2", function($cell){
                        $cell->setAlignment("center");
                        $cell->setValignment("center");
                        $cell->setFontSize(12);
                        $cell->setFontWeight('bold');
                    });
                    $dtObj["headerWidthBasic3"] = chr(ord($dtObj["headerWidthBasic3"]) + 2);
                }

                // create table total
                $dtObj["headerWidthBasic4"] = chr(ord($dtObj["headerWidthBasic"]) + 1);
                foreach ($headerList[3] as $headerList3)
                {
                    foreach ($headerList3 as $headerItem)
                    {
                        $dtObj["regCell"][] = $dtObj["headerWidthBasic4"]."3";
                        $sheet->cell($dtObj["headerWidthBasic4"]."3", $headerItem);
                        $sheet->cell($dtObj["headerWidthBasic4"]."3", function($cell){
                            $cell->setAlignment("center");
                            $cell->setValignment("center");
                            $cell->setFontSize(12);
                            $cell->setFontWeight('bold');
                        });
                        $dtObj["headerWidthBasic4"] = chr(ord($dtObj["headerWidthBasic4"]) + 1);
                    }
                }

                $startRow = 4;
                foreach ($valueList as $valueData)
                {
                    $sheet->row($startRow, $valueData);
                    $startRow++;
                }

                // create border
                foreach($dtObj["regCell"] as $regCellBorder)
                {
                    $sheet->cell($regCellBorder, function($cell){
                        $cell->setBorder("thin", "thin", "thin", "thin");
                    });
                }

            });
		})->store("xlsx", public_path("export/report"), true);

		return asset("export/report") . "/" . $filename . ".xlsx";
	}
}