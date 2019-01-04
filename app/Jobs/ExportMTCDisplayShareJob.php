<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\JobTrace;
use App\Traits\ExportMTCDisplayShareTrait;

class ExportMTCDisplayShareJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ExportMTCDisplayShareTrait;

    protected $trace, $periode, $id_employee, $id_store, $id_area, $limit, $filecode;

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
    public function __construct(JobTrace $trace, $periode, $id_employee, $id_store, $id_area, $limit, $filecode)
    {
        $this->trace = $trace;
        $this->periode = $periode;
        $this->id_employee = $id_employee;
        $this->id_store = $id_store;
        $this->id_area = $id_area;
        $this->limit = $limit;
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
            'results' => $this->MTCDisplayShareExportTrait($this->periode, $this->id_employee, $this->id_store, $this->id_area, $this->limit, $this->filecode), // return excel file location
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
