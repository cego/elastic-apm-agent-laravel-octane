<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Laravel\Octane\Events\RequestReceived;
use Cego\ElasticApmAgentLaravelOctane\OctaneApmManager;

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
        $manager = $event->sandbox->make(OctaneApmManager::class);

        $manager->beginTransaction($event->request->method() . ' ' . $event->request->route()->uri(), 'request');
        $manager->beginAndStoreSpan('RequestResponse', 'request');
    }
}
