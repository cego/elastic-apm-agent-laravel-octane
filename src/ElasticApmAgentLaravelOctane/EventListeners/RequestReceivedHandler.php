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
        /** @var OctaneApmManager $manager */
        $manager = $event->app->make(OctaneApmManager::class);

        $routeUri = $this->getRouteUri($event);
        $manager->beginTransaction($event->request->method() . ' /' . $routeUri, 'request');
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
            $routeUri = $router->getRoutes()->match($event->request)->uri();
            return $routeUri === "/" ? "" : $routeUri;
        } catch (Throwable $throwable) {
            // If the route does not exist, then simply return the path
            return $event->request->path();
        }
    }
}
