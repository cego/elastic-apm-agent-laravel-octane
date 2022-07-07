<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Laravel\Octane\Events\RequestTerminated;
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
        /** @var OctaneApmManager $manager */
        $manager = $event->sandbox->make(OctaneApmManager::class);

        if ($event instanceof RequestTerminated) {
            $manager->getTransaction()->setResult($this->getHttpResult($event));
        }

        $manager->endTransaction();
    }

    /**
     * Returns the HTTP Transaction Result
     *
     * @param RequestTerminated $event
     *
     * @return string
     */
    private function getHttpResult(RequestTerminated $event): string
    {
        $code = (string) $event->response->getStatusCode();

        return 'HTTP ' . $code[0] . str_repeat('x', strlen($code) - 1);
    }
}
