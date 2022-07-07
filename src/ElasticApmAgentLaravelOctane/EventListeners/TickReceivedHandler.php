<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Laravel\Octane\Events\TickReceived;
use Cego\ElasticApmAgentLaravelOctane\OctaneApmManager;

class TickReceivedHandler
{
    /**
     * Handle the event.
     *
     * @param  TickReceived  $event
     *
     * @return void
     */
    public function handle(TickReceived $event): void
    {
        /** @var OctaneApmManager $manager */
        $manager = $event->sandbox->make(OctaneApmManager::class);

        $manager->beginTransaction('Tick', 'tick');
    }
}
