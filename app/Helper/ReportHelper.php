<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Filters\SummaryFilters;
use App\SellInSummary;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;
use App\MtcReportTemplate;
use App\SalesMtcSummary;

class ReportHelper
{

    /**
     * constructor.
     */
    public function __construct()
    {

    }

    /**
     * MODELING
     */

    public function getTitle(Request $request){
        switch ($request->model) {
            case 'Sales MTC':
                $periode = Carbon::parse($request->periode);
                return 'Report Sales MTC '.$periode->format('F').' '.$periode->year;
                break;
        }
    }

    public function getModel(Request $request){
        switch ($request->model) {
            case 'Sell In':
                return $this->sellInModel($request);
                break;
            case 'Sales MTC':
                return $this->salesMtcModelByView($request);
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

    public function salesMtcModel(Request $request){

        $data = MtcReportTemplate::filter(new SummaryFilters($request))->get();
        $periode = Carbon::parse($request->periode);
        return
        [
            'filename' => 'Report Sales MTC '.$periode->format('F').' '.$periode->year.' @'.rand(10,99).Carbon::now()->format('his').rand(10,99),
            'title' => 'Report Sales MTC '.$periode->format('F').' '.$periode->year,
            'creator' => 'SASA',
            'description' => 'Sales MTC Data Reporting',
            'sheets' =>
                        [
                            [
                                'name' => 'SALES MTC',
                                'celling' => 'A1:T1',
                                'mapping' => $this->mapForExportSalesMtc($data)
                            ],
                        ]
        ];

    }

    public function salesMtcModelByView(Request $request){

        $data = SalesMtcSummary::filter(new SummaryFilters($request))->get();
        $periode = Carbon::parse($request->periode);
        return
        [
            'filename' => 'Report Sales MTC '.$periode->format('F').' '.$periode->year.' @'.rand(10,99).Carbon::now()->format('his').rand(10,99),
            'title' => 'Report Sales MTC '.$periode->format('F').' '.$periode->year,
            'creator' => 'SASA',
            'description' => 'Sales MTC Data Reporting',
            'sheets' =>
                        [
                            [
                                'name' => 'SALES MTC',
                                'celling' => 'A1:T1',
                                'mapping' => $this->mapForExportSalesMtcByView($data)
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

    public function mapForExportSalesMtc($data)
    {
        // $collection = collect($data);

        return $data->map(function ($item) {
            return [
                'PERIODE' => @$item->getSummary('periode'),
                'REGION' => @$item->getSummary('region'),
                'JAWA / NON JAWA' => @$item->getSummary('is_jawa'),
                'JABATAN' => @$item->getSummary('jabatan'),
                'NAMA' => @$item->getSummary('employee_name'),
                'AREA' => @$item->getSummary('area'),
                'SUB AREA' => @$item->getSummary('sub_area'),
                'OUTLET' => @$item->getSummary('store_name'),
                'ACCOUNT' => @$item->getSummary('account'),
                'CATEGORY' => @$item->getSummary('category'),
                'PRODUCT LINE' => @$item->getSummary('product_line'),
                'PRODUCT NAME' => @$item->getSummary('product_name'),
                'ACTUAL OUT QTY' => @$item->getSummary('actual_out_qty'),
                'ACTUAL IN QTY' => @$item->getSummary('actual_in_qty'),
                'PRICE' => @$item->getSummary('price'),
                'ACTUAL OUT VALUE' => @$item->getSummary('actual_out_value'),
                'ACTUAL IN VALUE' => @$item->getSummary('actual_in_value'),
                'TOTAL ACTUAL' => @$item->getSummary('total_actual'),
                'TARGET QTY' => @$item->getSummary('target_qty'),
                'TARGET VALUE' => @$item->getSummary('target_value'),
            ];
        });
    }

    public function mapForExportSalesMtcByView($data)
    {

        return $data->map(function ($item) {
            return [
                'PERIODE' => @$item->periode,
                'REGION' => @$item->region,
                'JAWA / NON JAWA' => @$item->is_jawa,
                'JABATAN' => @$item->jabatan,
                'NAMA' => @$item->employee_name,
                'AREA' => @$item->area,
                'SUB AREA' => @$item->sub_area,
                'OUTLET' => @$item->store_name,
                'ACCOUNT' => @$item->account,
                'CATEGORY' => @$item->category,
                'PRODUCT LINE' => @$item->product_line,
                'PRODUCT NAME' => @$item->product_name,
                'ACTUAL OUT QTY' => @$item->actual_out_qty,
                'ACTUAL IN QTY' => @$item->actual_in_qty,
                'PRICE' => @$item->price,
                'ACTUAL OUT VALUE' => @$item->actual_out_value,
                'ACTUAL IN VALUE' => @$item->actual_in_value,
                'TOTAL ACTUAL' => @$item->total_actual,
                'TARGET QTY' => @$item->target_qty,
                'TARGET VALUE' => @$item->target_value,
            ];
        });
    }

    /**
     * EXPORTING TEMPLATE
     */

    public function exporting(Request $request){

        $data = $this->getModel($request);

        try{
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


            })->store('xlsx', public_path('export/report'));
        }catch(\Exception $e){
            //
        }

        return [ 'filepath' => asset('export/report').'/'.$data['filename'].'.xlsx' ];

    }

    public function exportSalesMtc(SummaryFilters $filters){
        return 
        (new FastExcel(\App\MtcReportTemplate::filter($filters)))->download('testing.xlsx', function ($item) {
            return [
                'PERIODE' => $item->getSummary('periode'),
                'First Name' => 'DWI',
                'Last Name' => 'DIRGANTARA',
            ];
        });
    }

}