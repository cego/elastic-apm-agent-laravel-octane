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

        // APM uses mt_rand as a random source for transaction ids, which is seeded by system time.
        // When multiple workers start at the same time, there is a high chance of transaction id collisions.
        // Which breaks the APM view.
        // Therefore, we randomly seed the mt_rand method with a cryptographically secure source to avoid this.
        mt_srand(random_int(PHP_INT_MIN, PHP_INT_MAX));

        $transaction = ElasticApm::getCurrentTransaction();

        if ( ! $transaction->hasEnded()) {
            $transaction->setName('WorkerStart');
        }

        $transaction->end();
    }
}
