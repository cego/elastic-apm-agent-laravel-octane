<?php

namespace Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane\EventListeners;

use Elastic\Apm\ElasticApm;
use Elastic\Apm\SpanInterface;
use Elastic\Apm\TransactionInterface;
use Laravel\Octane\Events\RequestReceived;

class RequestReceivedHandler
{
    /**
     * Handle the event.
     *
     * @param  RequestReceived  $event
     *
     * @return void
     */
    public function handle(RequestReceived $event): void
    {
        $txName = $event->request->method() . ' ' . $event->request->path();

        $transaction = ElasticApm::getCurrentTransaction();

        if (! $transaction->hasEnded()) {
            $transaction->discard();
        }

        $event->sandbox->instance(TransactionInterface::class, $transaction = ElasticApm::beginCurrentTransaction(
            $txName,
            'request'
        ));
        $event->sandbox->instance(SpanInterface::class, $transaction->beginChildSpan(
            'RequestResponse',
            'request'
        ));
    }
}
