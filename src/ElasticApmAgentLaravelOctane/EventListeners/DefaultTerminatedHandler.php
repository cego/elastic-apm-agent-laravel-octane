<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Cego\ElasticApmAgentLaravelOctane\OctaneApmManager;

class DefaultTerminatedHandler
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     *
     * @return void
     */
    public function handle(object $event): void
    {
        $event->sandbox->make(OctaneApmManager::class)->endTransaction();
    }
}
