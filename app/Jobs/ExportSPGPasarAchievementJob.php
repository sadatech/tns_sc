<?php
namespace App\Jobs;

use Exception;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\JobTrace;
use App\Traits\ExportSPGPasarAchievementTrait;

class ExportSPGPasarAchievementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ExportSPGPasarAchievementTrait;

    protected $trace, $filecode;

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
    public function __construct(JobTrace $trace, $filecode)
    {
        $this->trace = $trace;
        $this->filecode = $filecode;
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
            'results' => $this->SPGPasarAchievementExportTrait($this->filecode), // return excel file location
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
