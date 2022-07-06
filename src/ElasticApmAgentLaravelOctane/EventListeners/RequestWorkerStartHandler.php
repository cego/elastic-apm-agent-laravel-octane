<?php

namespace Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane\EventListeners;

use Elastic\Apm\ElasticApm;
use Laravel\Octane\Events\WorkerStarting;

class RequestWorkerStartHandler
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
        $transaction = ElasticApm::getCurrentTransaction();
        if (! $transaction->hasEnded()) {
            $transaction->setName('WorkerStart');
        }

        $transaction->end();
    }
}
