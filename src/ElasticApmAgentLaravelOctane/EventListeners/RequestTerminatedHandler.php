<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Elastic\Apm\TransactionInterface;
use Laravel\Octane\Events\RequestTerminated;

class RequestTerminatedHandler
{
    /**
     * Handle the event.
     *
     * @param  RequestTerminated  $event
     *
     * @return void
     */
    public function handle(RequestTerminated $event): void
    {
        /** @var TransactionInterface $transaction */
        $transaction = $event->sandbox->make(TransactionInterface::class);
        $transaction->end();
    }
}
