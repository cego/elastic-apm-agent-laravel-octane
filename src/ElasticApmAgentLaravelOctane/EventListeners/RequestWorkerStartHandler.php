<?php

namespace App\Octane\Listeners;

use Elastic\Apm\ElasticApm;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Events\WorkerStarting;

class ElasticApmWorkerStartingHandler
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     *
     * @return void
     */
    public function handle(WorkerStarting $event): void
    {
        if (! ElasticApm::getCurrentTransaction()->hasEnded()) {
            ElasticApm::getCurrentTransaction()->setName('WorkerStart');
        }

        Log::info(__METHOD__ . ' ' . class_basename($event));

        ElasticApm::getCurrentTransaction()->end();
    }
}
