<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

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
        if( ! class_exists(ElasticApm::class)){
            return;
        }
        
        $transaction = ElasticApm::getCurrentTransaction();

        if ( ! $transaction->hasEnded()) {
            $transaction->setName('WorkerStart');
        }

        $transaction->end();
    }
}
