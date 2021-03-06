<?php

namespace Cego\ElasticApmAgentLaravelOctane\EventListeners;

use Throwable;
use Illuminate\Routing\Router;
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

        $manager->beginTransaction($event->request->method() . ' ' . $this->getRouteUri($event), 'request');
        $manager->beginAndStoreSpan('RequestResponse', 'request');
    }

    /**
     * Returns the request route uri
     *
     * @param RequestReceived $event
     *
     * @return string
     */
    private function getRouteUri(RequestReceived $event): string
    {
        /** @var Router $router */
        $router = $event->sandbox->make('router');

        try {
            return $router->getRoutes()->match($event->request)->uri();
        } catch (Throwable $throwable) {
            // If the route does not exist, then simply return the path
            return $event->request->path();
        }
    }
}
