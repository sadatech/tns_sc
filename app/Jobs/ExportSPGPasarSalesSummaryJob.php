<?php
namespace App\Jobs;

use Exception;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\JobTrace;
use App\Traits\ExportSPGPasarSalesSummaryTrait;

class ExportSPGPasarSalesSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ExportSPGPasarSalesSummaryTrait;

    protected $trace, $params;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobTrace $trace, $params = [])
    {
        $this->trace = $trace;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->trace->update([
            'status' => 'DONE',
            'results' => $this->SPGPasarSalesSummaryExportTrait($this->params[0], $this->params[1], $this->params[2]), // return excel file location
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
        return $this->trace->update([
            'status' => 'FAILED',
            'log' => $exception->getMessage(),
        ]);
    }
}
