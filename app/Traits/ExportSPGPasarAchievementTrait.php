<?php
namespace App\Traits;

use DB;
use Excel;

use App\Model\Extend\SalesSpgPasarAchievement;

trait ExportSPGPasarAchievementTrait
{

	public function SPGPasarAchievementExportTrait()
	{
        $sales = SalesSpgPasarAchievement::whereNull('deleted_at')
        ->groupBy(DB::raw("CONCAT_WS('-',MONTH(date),YEAR(date))"), DB::raw('id_employee'))
        ->orderBy(DB::raw("CONCAT_WS('-',MONTH(date),YEAR(date))"), 'ASC')
        ->orderBy('id_employee', 'ASC');
        //
        $filename = "SPG-Pasar_Report-Achievement";
        $store = Excel::create($filename, function($excel) use ($sales){
            $excel->sheet("Achievement", function($sheet) use ($sales){

                //
                $dtObj = [];

                // list header
                $dtObj["listHeader"] = ["Periode", "Area", "Nama SPG", "HK", "Sum Of Jumlah", "Sum Of PF Value", "Sum Of Total Value", "EFF. Kontak", "Value", "Sales/Kontak"];

                // all width
                $dtObj["allWidth"] = "@";
                foreach ($dtObj["listHeader"] as $__header)
                {
                    $dtObj["allWidth"] = chr(ord($dtObj["allWidth"]) + 1);
                    $dtObj["allWidthObj"][] = $dtObj["allWidth"];
                }

                // list value
                $startVal = 3;
                $dtObj["allHeightObj"] = [1, 2, 3];
                foreach ($sales->get() as $sales_data)
                {
                    $dtObj["dataValue"][$startVal] = [
                        $sales_data->periode,
                        $sales_data->area,
                        $sales_data->nama_spg,
                        $sales_data->hk,
                        $sales_data->sum_of_jumlah,
                        $sales_data->sum_of_pf_value,
                        $sales_data->sum_of_total_value,
                        $sales_data->eff_kontak,
                        $sales_data->act_value,
                        $sales_data->sales_per_kontak,
                    ];
                    $startVal++;
                    $dtObj["allHeightObj"][] = $startVal;
                    $dtObj["allHeight"] = $startVal;
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

                // set height
                $sheet->setHeight(2, 50);

                // center header
                $sheet->row(2, $dtObj["listHeader"]);
                $sheet->row(2, function($row){
                    $row->setAlignment("center");
                    $row->setValignment("center");
                    $row->setFontWeight('bold');
                    $row->setFontSize(16);
                });

                //
                foreach ($dtObj["dataValue"] as $key => $data_value)
                {
                    $sheet->row($key, $data_value);
                }

            });
        })->store("xlsx", public_path("export/report"), true);

        return asset("export/report") . "/" . $filename . ".xlsx";
	}

}