<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Laravel\Octane\Events\RequestTerminated;
use Cego\ElasticApmAgentLaravelOctane\OctaneApmManager;

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
        $event->sandbox->make(OctaneApmManager::class)->endTransaction();
    }
}
