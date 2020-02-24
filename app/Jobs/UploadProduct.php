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

class UploadProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 3600;

    protected $trace;

    public function __construct(JobTrace $trace)
    {
        $this->trace = $trace;
    }

    public function handle()
    {
        Excel::selectSheets('Product')->load('public/imports/product/'.$this->trace->file_name, function($reader)
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

                $productName = trim($value);
                if(empty($chunked['name']) || $chunked['name'] == '-'){
                    $log_error .= 'Untuk data row ke-'.$i.' kolom "NAME" tidak boleh kosong.';
                    $this->trace->update(['log' => $log_error]);
                    continue;
                }

                if(empty($chunked['panel']) || $chunked['panel'] == '-'){
                    $log_error .= 'Untuk data row ke-'.$i.' kolom "PANEL" tidak boleh kosong.';
                    $this->trace->update(['log' => $log_error]);
                    continue;
                }

                $subCategoryName = trim($chunked['sub_category']);
                if(empty($subCategoryName) || $subCategoryName == '-'){
                    $log_error .= 'Untuk data row ke-'.$i.' kolom "SUB CATEGORY" tidak boleh kosong.';
                    $this->trace->update(['log' => $log_error]);
                    continue;
                }
                $subCategory = SubCategory::whereRaw( "upper(name) = '". strtoupper($subCategoryName)  ."'")->first();
                if(!$subCategory){
                    $log_error .= 'Untuk data row ke-'.$i.' tidak bisa menemukan data "SUB CATEGORY" dengan Nama "'.$subCategoryName.'". Harap di cek kembali.';
                    $this->trace->update(['log' => $log_error]);
                    continue;
                }

                $dataProduct['sub_category_name']   = $row->sub_category;
                $dataProduct['category_name']       = $row->category;
                $dataProduct['brand_name']          = $row->brand;
                $id_sub_category = $this->findSub($dataProduct);

                $product = Product::firstOrCreate([
                    'id_sub_category'   => $subCategory->id,
                    'code'              => $dataArray['code'],
                    'name'              => $dataArray['name'],
                    'panel'             => strtolower($dataArray['panel']),
                    'pcs'               => $dataArray['pcs'],
                    'pack'              => $dataArray['pack'],
                    'carton'            => $dataArray['carton'],
                ]);

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
                'status'    => 'FAILED',
                'log'       => 'SYSTEM ERROR : '.$exception->getMessage(),
            ]);
    }
}
