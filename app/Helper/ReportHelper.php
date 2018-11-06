<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Filters\SummaryFilters;
use App\SellInSummary;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportHelper
{

    /**
     * constructor.
     */
    public function __construct(Request $request)
    {

    }

    /**
     * MODELING
     */

    public function getModel(Request $request){
        switch ($request->model) {
            case 'Sell In':
                return $this->sellInModel($request);
                break;
        }
    }

    public function sellInModel(Request $request){

        $data = SellInSummary::filter(new SummaryFilters($request))->get();
        $date_range = explode('|', $request->date_range);
        return
        [
            'filename' => 'Report Sell In '.$date_range[0].' - '.$date_range[1],
            'title' => 'Report Sell In',
            'creator' => 'SASA',
            'description' => 'Sell In Data Reporting',
            'sheets' =>
                        [
                            [
                                'name' => 'SELL IN',
                                'celling' => 'A1:S1',
                                'mapping' => $this->mapForExportSales($data)
                            ],
                            [
                                'name' => 'SELL IN 2',
                                'celling' => 'A1:S1',
                                'mapping' => $this->mapForExportSales($data)
                            ],
                        ]
        ];

    }

    /**
     * MAPPING
     */

    public function mapForExportSales($data)
    {
        $collection = collect($data);

        return $collection->map(function ($item) {
            return [
                'WEEK' => @$item->week,
                'REGION' => @$item->region,
                'AREA' => @$item->area,
                'SUB AREA' => @$item->sub_area,
                'ACCOUNT' => @$item->account,
                'CHANNEL' => @$item->channel,
                'STORE NAME 1' => @$item->store_name_1,
                'STORE NAME 2' => @$item->store_name_2,
                'NIK' => @$item->nik,
                'EMPLOYEE NAME' => @$item->employee_name,
                'DATE' => @$item->date,
                'PRODUCT' => @$item->product_name,
                'CATEGORY' => @$item->category,
                'ACTUAL QUANTITY' => number_format(@$item->actual_qty),
                'MEASUREMENT' => @$item->measure_name,
                'CONVERTION QUANTITY' => number_format(@$item->qty),
                'UNIT PRICE' => number_format(@$item->unit_price),
                'VALUE' => number_format(@$item->value),
                'VALUE PF' => number_format(@$item->value_pf),
            ];
        });
    }

    public function mapForExportSalesNew($data)
    {
        // $collection = collect($data);

        return $data->map(function ($item) {
            return [
                'WEEK' => @$item->week,
                'REGION' => @$item->region,
                'AREA' => @$item->area,
                'SUB AREA' => @$item->sub_area,
                'ACCOUNT' => @$item->account,
                'CHANNEL' => @$item->channel,
                'STORE NAME 1' => @$item->store_name_1,
                'STORE NAME 2' => @$item->store_name_2,
                'NIK' => @$item->nik,
                'EMPLOYEE NAME' => @$item->employee_name,
                'DATE' => @$item->date,
                'PRODUCT' => @$item->product_name,
                'CATEGORY' => @$item->category,
                'ACTUAL QUANTITY' => number_format(@$item->actual_qty),
                'MEASUREMENT' => @$item->measure_name,
                'CONVERTION QUANTITY' => number_format(@$item->qty),
                'UNIT PRICE' => number_format(@$item->unit_price),
                'VALUE' => number_format(@$item->value),
                'VALUE PF' => number_format(@$item->value_pf),
            ];
        });
    }

    /**
     * EXPORTING TEMPLATE
     */

    public function exporting($data){

        return 
        Excel::create($data['filename'], function($excel) use ($data) {

            // Set the title
            $excel->setTitle($data['title']);

            // Chain the setters
            $excel->setCreator($data['creator'])
                  ->setCompany($data['creator']);

            // Call them separately
            $excel->setDescription($data['description']);

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            foreach ($data['sheets'] as $item) {

                $excel->sheet($item['name'], function ($sheet) use ($item) {
                    $sheet->setAutoFilter($item['celling']);
                    $sheet->setHeight(1, 25);
                    $sheet->fromModel($item['mapping'], null, 'A1', true, true);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#82abde');
                    });
                    $sheet->cells($item['celling'], function ($cells) {
                        $cells->setFontWeight('bold');
                    });
                    $sheet->setBorder($item['celling'], 'thin');
                });

            }


        })->string('xlsx');

    }

}