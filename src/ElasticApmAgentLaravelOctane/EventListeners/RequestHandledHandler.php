<?php

namespace Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane\EventListeners;

use Elastic\Apm\SpanInterface;
use Elastic\Apm\TransactionInterface;
use Laravel\Octane\Events\RequestHandled;

class RequestHandledHandler
{
    /**
     * Handle the event.
     *
     * @param  RequestHandled  $event
     *
     * @return void
     */
    public function handle(RequestHandled $event): void
    {
        /** @var SpanInterface $span */
        $span = $event->sandbox->make(SpanInterface::class);
        $span->end();
    }
}
