<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Area;
use App\Product;
use App\ProductFocus;
use App\ProductFocusArea;
use App\JobTrace;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class UploadFocus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 3600;

    protected $trace;

    public function __construct(JobTrace $trace)
    {
        $this->trace = $trace;
    }

    public function handle()
    {
        Excel::selectSheets('Focus')->load('public/imports/product-focus/'.$this->trace->file_name, function($reader)
        {
            // Getting all results
            $results = $reader->get();

            $i = 1;
            foreach($results as $chunked)
            {
                /* CHECK IF ANY ERROR */
                if(!empty($this->trace->log)){
                    break;
                }

                $dataArray = array();
                $i += 1;
                $log_error = $this->trace->log;

                $startDate = '-';
                $endDate = '-';
                try{
                    $startD = explode('/', $chunked['start_month']);
                    $endD   = explode('/', $chunked['end_month']);
                    $startDate = Carbon::parse(trim($startD[0]).'/01/'.trim($startD[1]))->format('Y-m-d');
                    $endDate = Carbon::parse(trim($endD[0]).'/01/'.trim($endD[1]))->endOfMonth()->format('Y-m-d');
                }catch(\Exception $e){
                }

                if($startDate == '-'){
                    $log_error .= 'Untuk data row ke-'.$i.' kolom "START MONTH" tidak sesuai aturan yang berlaku. Harap di cek kembali.';
                    $this->trace->update(['log' => $log_error]);
                    continue;
                }

                if($endDate == '-'){
                    $log_error .= 'Untuk data row ke-'.$i.' kolom "END MONTH" tidak sesuai aturan yang berlaku. Harap di cek kembali.';
                    $this->trace->update(['log' => $log_error]);
                    continue;
                }
                $dataArray['start_date']    = $startDate;
                $dataArray['end_date']      = $endDate;

                $productArray = explode(',', $chunked['product_name']);
                foreach ($productArray as $key => $value) {
                    $productName = trim($value);
                    
                    $product = Product::whereRaw( "upper(name) = '". strtoupper(trim($productName))  ."'")->first();
                    if(!$product){
                        $log_error .= 'Untuk data row ke-'.$i.' tidak bisa menemukan data "'.strtoupper('product').'" dengan Nama "'.$productName.'". Harap di cek kembali.';
                        $this->trace->update(['log' => $log_error]);
                        continue;
                    }
                    
                    $focus = ProductFocus::firstOrCreate([
                        'id_product'    => $product->id,
                        'from'          => $dataArray['start_date'],
                        'to'            => $dataArray['end_date'],
                    ]);

                    $areaArray = explode(',', $chunked['area_name']);
                    foreach ($areaArray as $key => $value) {
                        $areaName = trim($value);
                    
                        $area = Area::whereRaw( "upper(name) = '". strtoupper(trim($areaName))  ."'")->first();
                        if(!$area){
                            $log_error .= 'Untuk data row ke-'.$i.' tidak bisa menemukan data "'.strtoupper('area').'" dengan Nama "'.$areaName.'". Harap di cek kembali.';
                            $this->trace->update(['log' => $log_error]);
                            continue;
                        }
                        
                        $focusArea = ProductFocusArea::firstOrCreate([
                            'id_product_focus'  => $focus->id,
                            'id_area'           => $area->id,
                        ]);
                    }
                }

            }

        });

        if($this->trace->log == '' || $this->trace->log == null){
            $this->trace->update([
                'status' => 'DONE',
            ]);
        }else{
            $this->trace->update([
                'status' => 'FAILED',
            ]);
        }

    }

    public function failed(\Exception $exception)
    {
        $this->trace->update([
                'status' => 'FAILED',
                'log' => 'SYSTEM ERROR : '.$exception->getMessage(),
            ]);
    }
}
