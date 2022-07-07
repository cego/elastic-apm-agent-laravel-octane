<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Laravel\Octane\Events\TaskReceived;
use Cego\ElasticApmAgentLaravelOctane\OctaneApmManager;

class TaskReceivedHandler
{
    /**
     * Handle the event.
     *
     * @param  TaskReceived  $event
     *
     * @return void
     */
    public function handle(TaskReceived $event): void
    {
        /** @var OctaneApmManager $manager */
        $manager = $event->sandbox->make(OctaneApmManager::class);

        $manager->beginTransaction('Task');
    }
}
