<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Laravel\Octane\Events\RequestHandled;
use Cego\ElasticApmAgentLaravelOctane\OctaneApmManager;

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
        $event->sandbox->make(OctaneApmManager::class)->endStoredSpan('RequestResponse');
    }
}
