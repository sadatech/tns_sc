<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\JobTrace;
use Carbon\Carbon;
use Exception;
use File;
use Maatwebsite\Excel\Facades\Excel;
use Auth;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    protected $trace, $params, $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobTrace $trace, $params, $user)
    {
        //
        $this->trace = $trace;
        $this->params = $params;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // PROCESSING FILE
        $companyFolderPath = storage_path('export/'.$this->user->id_company);
        File::isDirectory($companyFolderPath) or File::makeDirectory($companyFolderPath, 0777, true, true);

        $date_explode = explode(' ', $this->trace->date);
        $filename = $this->params['title'].' '.$date_explode[0].' '.str_replace(':', '', $date_explode[1]);        

        if($this->params['type'] == 'SELECTED'){ // === SELECTED ===

            $data = json_decode($this->params['data'], true);
            $temp_params = $this->params;

            $excel = Excel::create($filename, function($excel) use ($data, $temp_params) {

                // Set the title
                $excel->setTitle($temp_params['title']);

                // Chain the setters
                $excel->setCreator('SADA')
                      ->setCompany('SADA');

                // Call them separately
                $excel->setDescription($temp_params['description']);

                $excel->getDefaultStyle()
                    ->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

                $excel->sheet($temp_params['sheet_name'], function ($sheet) use ($data) {
                    $sheet->setHeight(1, 25);
                    $sheet->fromArray($data);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#82abde');
                    });
                });


            })->store('xlsx', $companyFolderPath);

        }else{ // === ALL ===

        }

        $this->trace->update([
            'status' => 'DONE',
            'results' => $companyFolderPath.'/'.$this->params['title'].'.xlsx',
        ]);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
        $this->trace->update([
                'status' => 'FAILED',
                'log' => $exception->getMessage(),
            ]);
    }

}
