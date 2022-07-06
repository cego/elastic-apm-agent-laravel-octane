<?php

namespace Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane;

use Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane\EventListeners\RequestHandledHandler;
use Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane\EventListeners\RequestReceivedHandler;
use Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane\EventListeners\RequestTerminatedHandler;
use Cego\ElasticApmAgentLaravelOctane\ElasticApmAgentLaravelOctane\EventListeners\RequestWorkerStartHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Facades\Octane;
use Illuminate\Support\ServiceProvider;

class ElasticApmAgentLaravelOctaneServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {}

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->app->make(Dispatcher::class);

        $dispatcher->listen(RequestTerminated::class, RequestTerminatedHandler::class);
        $dispatcher->listen(RequestHandled::class, RequestHandledHandler::class);
        $dispatcher->listen(RequestReceived::class, RequestReceivedHandler::class);
        $dispatcher->listen(WorkerStarting::class, RequestWorkerStartHandler::class);
    }
}
