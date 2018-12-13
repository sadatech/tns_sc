<?php
namespace App\Traits;

use Carbon\Carbon;
use DB;
use Excel;

use App\Product;
use App\Model\Extend\SalesSpgPasarSummary;
use App\SubCategory;
use App\ProductFokusSpg;

trait ExportSPGPasarSalesSummaryTrait
{
	public function SPGPasarSalesSummaryExportTrait($id_subcategory, $filterMonth)
	{
        $periode = Carbon::parse($filterMonth)->format('Y-m-d');

        $products = ProductFokusSpg::whereHas('product', function($query) use ($id_subcategory){
            return $query->where('id_subcategory', $id_subcategory);
        })
        ->whereDate('from', '<=', $periode)
        ->whereDate('to', '>=', $periode)
        ->get();

        $sub_category_detail = SubCategory::where("id", $id_subcategory)->first();
        $sub_cat = array_unique($products->pluck('product.subcategory.id')->toArray());

        $sales = SalesSpgPasarSummary::whereHas('detailSales.product.subcategory', function ($query) use ($id_subcategory, $periode, $sub_cat){
            return $query->where('id', $id_subcategory)->whereIn('id', $sub_cat);
        })
        ->whereMonth('date', Carbon::parse($periode)->month)
        ->whereYear('date', Carbon::parse($periode)->year)
        ->groupBy('id_employee', 'id_pasar', 'date')
        ->orderBy('date', 'DESC')
        ->orderBy('id_employee', 'ASC')
        ->orderBy('id_pasar', 'ASC')->get();

        // Export Excel
        $filename = "SPG Pasar - Report Harian " . str_replace(" ", "_", $sub_category_detail->name)." - ".Carbon::parse($filterMonth)->format("M-Y") . " (#".str_replace("-", null, crc32(md5(time()))).")";
        Excel::create($filename, function($excel) use ($sales, $sub_category_detail){
            $excel->sheet("Summary", function($sheet) use ($sales, $sub_category_detail){

                $dtObj = [];
                $dtObj["tmpDtDynamic"] = [];
                $dtObj["widthRow"] = "@";
                $dtObj["heightSheet"] = 2;
                $start_ALP = "A";
                $start_NUM = 1;

                $dtObj["listHeader"] = ["No", "Area", "Nama SPG", "Tanggal", "Nama Pasar", "Nama Stokis/Grosir", "Jumlah Konsumen Beli"];
                foreach ($sales as $sales_data)
                {
                    foreach ($sales_data->product_focus_list as $sales_data_product_focus_list) 
                    {
                        if (!in_array(Product::where("id", $sales_data_product_focus_list)->first()->name, $dtObj["listHeader"]))
                        {
                            array_push($dtObj["tmpDtDynamic"], time());
                            array_push($dtObj["listHeader"], Product::where("id", $sales_data_product_focus_list)->first()->name);
                        }
                    }
                }
                $dtObj["listHeader"] = array_merge($dtObj["listHeader"], ["Sales Other", "Sales Product Fokus", "Total Value", "Keterangan"]);

                //
                foreach ($dtObj["listHeader"] as $__header)
                {
                    $dtObj["widthRow"] = chr(ord($dtObj["widthRow"]) + 1);
                }

                //
                $dtObj["mergeCell"]  = $start_ALP.$start_NUM.":".$dtObj["widthRow"].$start_NUM;
                //

                //
                $dtObj["dataValue"] = [];
                foreach ($sales as $sales_key => $sales_data)
                {
                    $dtObj["heightSheet"]++;
                    $dtObj["dataValue"][$sales_key] = [
                        ($sales_key + 1),
                        $sales_data->area, // area
                        $sales_data->nama_spg, // nama spg
                        Carbon::parse($sales_data->tanggal)->format('d/m/Y'), // nama spg
                        $sales_data->nama_pasar, // nama pasar
                        $sales_data->nama_stokies, // nama stokis
                        $sales_data->jumlah_beli, // jumlah beli
                    ];
                    foreach ($sales_data->detail as $sales_data_product_focus_list) 
                    {
                        array_push($dtObj["dataValue"][$sales_key], number_format($sales_data_product_focus_list));
                    }
                    $dtObj["dataValue"][$sales_key] = array_merge($dtObj["dataValue"][$sales_key], [
                        number_format($sales_data->sales_other),
                        number_format($sales_data->sales_pf),
                        number_format($sales_data->total_value),
                        ""]);
                }

                // create title
                $sheet->mergeCells($dtObj["mergeCell"]);
                $sheet->row(1, ["Report Harian " . $sub_category_detail->name]);
                $sheet->row(1, function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontWeight('bold');
                    $row->setFontSize(16);
                });

                // skip row 2
                $sheet->setHeight(2, 1);

                // create header
                $sheet->row(3, $dtObj["listHeader"]);
                $sheet->setHeight(3, 50);
                $sheet->row(3, function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                });

                // create all width
                $dtObj["allWidth"] = "@";
                foreach ($dtObj["listHeader"] as $__header)
                {
                    $dtObj["allWidth"] = chr(ord($dtObj["allWidth"]) + 1);
                    $dtObj["allWidthObj"][] = $dtObj["allWidth"];
                }

                // freeze
                $sheet->setFreeze($dtObj["allWidthObj"][0 + 3]."4");

                // create all height
                $dtObj["allHeight"] = 3;
                $dtObj["allHeightObj"][] = $dtObj["allHeight"];
                foreach ($sales as $sale_data)
                {
                    $dtObj["allHeight"] = ($dtObj["allHeight"] + 1);
                    $dtObj["allHeightObj"][] = $dtObj["allHeight"];
                }

                // create all border
                foreach ($dtObj["allWidthObj"] as $__allWidth)
                {
                    foreach ($dtObj["allHeightObj"] as $__allHeight)
                    {
                        $sheet->cell($__allWidth.$__allHeight, function($cell){
                            $cell->setBorder("thin", "thin", "thin", "thin");
                        });
                    }
                }

                // create all value
                $startRow = 3;
                foreach ($dtObj["dataValue"] as $__dataValue)
                {
                    $startRow++;
                    $sheet->row($startRow, $__dataValue);
                }

                // center tanggal
                $sheet->cells($dtObj["allWidthObj"][0 + 3].($dtObj["allHeightObj"][0] + 1).":".$dtObj["allWidthObj"][0 + 3].($dtObj["allHeight"] + 1), function($cell){
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                });
                $sheet->cells($dtObj["allWidthObj"][0 + 6].($dtObj["allHeightObj"][0] + 1).":".$dtObj["allWidthObj"][0 + 6].($dtObj["allHeight"] + 1), function($cell){
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                });
                $sheet->setColumnFormat([
                    $dtObj["allWidthObj"][0 + 3].($dtObj["allHeightObj"][0] + 1).":"."D".($dtObj["allHeight"] + 1) => "yyyy-mm-dd"
                ]);

                // center dynamic data
                $startDyn = $dtObj["allWidthObj"][0 + 6];
                foreach ($dtObj["tmpDtDynamic"] as $__tmpDtDynamic)
                {
                    $startDyn = chr(ord($startDyn) + 1);
                    $sheet->cells($startDyn.($dtObj["allHeightObj"][0] + 1).":".$startDyn.($dtObj["allHeight"] + 1), function($cell){
                        $cell->setAlignment("center");
                        $cell->setValignment("center");
                    });
                }

                // center 2 last column
                $sheet->cells(chr(ord($dtObj["widthRow"]) - 1).($dtObj["allHeightObj"][0] + 1).":".chr(ord($dtObj["widthRow"]) - 1).($dtObj["allHeight"] + 1), function($cell){
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                });
                $sheet->cells(chr(ord($dtObj["widthRow"]) - 2).($dtObj["allHeightObj"][0] + 1).":".chr(ord($dtObj["widthRow"]) - 2).($dtObj["allHeight"] + 1), function($cell){
                    $cell->setAlignment("center");
                    $cell->setValignment("center");
                });

                // colorize header
                for ($i=0; $i < 6; $i++)
                {
                    $sheet->cell($dtObj["allWidthObj"][$i]."3", function($cell){
                        $cell->setBorder("thin", "thin", "thin", "thin");
                        $cell->setBackground('#f4b084');
                        $cell->setFontWeight('bold');
                    });
                }
                $sheet->cell($dtObj["allWidthObj"][0 + 6]."3", function($cell){
                    $cell->setBorder("thin", "thin", "thin", "thin");
                    $cell->setBackground('#82abde');
                    $cell->setFontWeight('bold');
                });
                for ($i=7; $i < (7 + count($dtObj["tmpDtDynamic"])); $i++)
                {
                    $sheet->cell($dtObj["allWidthObj"][$i]."3", function($cell){
                        $cell->setBorder("thin", "thin", "thin", "thin");
                        $cell->setBackground('#a9d08e');
                        $cell->setFontWeight('bold');
                    });
                }
                $sheet->cell($dtObj["allWidthObj"][(7 + count($dtObj["tmpDtDynamic"]) + 0)]."3", function($cell){
                    $cell->setBorder("thin", "thin", "thin", "thin");
                    $cell->setBackground('#82abde');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell($dtObj["allWidthObj"][(7 + count($dtObj["tmpDtDynamic"]) + 1)]."3", function($cell){
                    $cell->setBorder("thin", "thin", "thin", "thin");
                    $cell->setBackground('#a9d08e');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell($dtObj["allWidthObj"][(7 + count($dtObj["tmpDtDynamic"]) + 2)]."3", function($cell){
                    $cell->setBorder("thin", "thin", "thin", "thin");
                    $cell->setBackground('#f4b084');
                    $cell->setFontWeight('bold');
                });
                $sheet->cell($dtObj["allWidthObj"][(7 + count($dtObj["tmpDtDynamic"]) + 3)]."3", function($cell){
                    $cell->setBorder("thin", "thin", "thin", "thin");
                    $cell->setBackground('#ffc000');
                    $cell->setFontWeight('bold');
                });

            });
        })->store("xlsx", public_path("export/report"), true);

        return asset("export/report") . "/" . $filename . ".xlsx";
	}
}