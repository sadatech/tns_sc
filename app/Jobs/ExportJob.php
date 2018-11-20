<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use App\JobTrace;
use Carbon\Carbon;
use Exception;
use File;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use App\Helper\ReportHelper as ReportHelper;
use DB;

class ExportJob implements ShouldQueue
{
    protected $reportHelper;

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
        $this->reportHelper = new ReportHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // DB::transaction(function () {
        //     try{

                $excel = $this->reportHelper->exporting(new Request($this->params));

                $this->trace->update([
                    'status' => 'DONE',
                    'results' => $excel['filepath'],
                ]);

        //     }catch(\Exception $exception){
        //         DB::rollback();

        //         $this->trace->update([
        //             'status' => 'FAILED',
        //             'log' => $exception->getMessage(),
        //         ]);
        //     }
        // });
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
