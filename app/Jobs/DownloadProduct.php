<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use App\Area;
use App\Product;
use App\ProductFocus;
use App\ProductFocusArea;
use App\JobTrace;
use App\Filters\ProductFocusFilters;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\ExcelHelper as ExcelHelper;

class DownloadProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 1;
    public $timeout = 3600;

    protected $trace, $params, $excelHelper;

    public function __construct(JobTrace $trace, $params)
    {
        $this->trace        = $trace;
        $this->params       = $params;
        $this->excelHelper  = new ExcelHelper;
    }

    public function handle()
    {
        $data       = [];
        $newParams  = $this->params;
        $filters    = new ProductFocusFilters(new Request($newParams));
        $focus      = ProductFocus::filter($filters)->get();

        foreach ($focus as $index => $fc) {
            $area = @$fc->productFocusArea()
                ->join('areas','areas.id','product_focus_areas.id_area')
                ->pluck('name')->toArray();

            $data[$index]['product']        = @$fc->product->name;
            $data[$index]['area']           = count($area) > 0 ? implode(', ', $area) : 'ALL';
            $data[$index]['start_month']    = $fc->from;
            $data[$index]['end_month']      = $fc->to;
        }

        $excel = Excel::create($this->trace->file_name, function($excel) use ($data) {

            // Set the title
            $excel->setTitle('Master - Product');

            // Chain the setters
            $excel->setCreator('TNS')
                  ->setCompany('TNS');

            // Call them separately
            $excel->setDescription('Product Data');

            $excel->getDefaultStyle()
                ->getAlignment()
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $excel->sheet('Product', function ($sheet) use ($data) {
                $sheet->setAutoFilter('A1:D1');
                $sheet->setHeight(1, 25);
                $sheet->fromModel($this->excelHelper->mapForProductFocus($data), null, 'A1', true, true);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#82abde');
                });
                $sheet->cells('A1:D1', function ($cells) {
                    $cells->setFontWeight('bold');
                });
                $sheet->setBorder('A1:D1', 'thin');
            });

        })->store("xlsx", public_path($this->trace->file_path), true);

        $this->trace->update([
            'status'    => 'DONE',
            'results'   => $this->trace->file_path. "/" . $this->trace->file_path . "/" . $this->trace->file_name . ".xlsx",
            'file_name' => $this->trace->file_name.".xlsx",
        ]);

        $this->trace->update([
            'status' => 'DONE',
        ]);

    }

    public function failed(\Exception $exception)
    {
        $this->trace->update([
                'status'    => 'FAILED',
                'log'       => 'SYSTEM ERROR : '.$exception->getMessage(),
            ]);
    }
}
