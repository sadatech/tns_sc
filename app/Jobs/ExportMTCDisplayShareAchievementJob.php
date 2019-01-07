<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\JobTrace;
use App\Traits\ExportMTCDisplayShareAchievementTrait;

class ExportMTCDisplayShareAchievementJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ExportMTCDisplayShareAchievementTrait;

    protected $trace, $limitArea, $limitSpg, $limitMD, $filecode;

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
    public function __construct(JobTrace $trace, $limitArea, $limitSpg, $limitMD, $filecode)
    {
        $this->trace = $trace;
        $this->limitArea = $limitArea;
        $this->limitSpg = $limitSpg;
        $this->limitMD = $limitMD;
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
            'results' => $this->MTCDisplayShareAchievementExportTrait($this->limitArea, $this->limitSpg, $this->limitMD, $this->filecode), // return excel file location
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
