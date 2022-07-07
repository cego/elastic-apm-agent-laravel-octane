<?php

namespace Cego\ElasticApmAgentLaravelOctane;

use Cego\ElasticApmAgentLaravelOctane\EventListeners\RequestReceivedHandler;
use Cego\ElasticApmAgentLaravelOctane\EventListeners\TaskReceivedHandler;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\TaskTerminated;
use Laravel\Octane\Events\TickTerminated;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\RequestReceived;
use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Octane\Events\RequestTerminated;
use Illuminate\Contracts\Container\BindingResolutionException;
use Cego\ElasticApmAgentLaravelOctane\EventListeners\TickReceivedHandler;
use Cego\ElasticApmAgentLaravelOctane\EventListeners\RequestHandledHandler;
use Cego\ElasticApmAgentLaravelOctane\EventListeners\DefaultTerminatedHandler;
use Cego\ElasticApmAgentLaravelOctane\EventListeners\RequestWorkerStartHandler;

class ElasticApmAgentLaravelOctaneServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // NOTE: It is NOT allowed to resolve the OctaneApmManager outside the sandbox.
        // Doing this would make the Manager a singleton across all workers and thereby all requests.
        $this->app->singleton(OctaneApmManager::class, function () {
            return new OctaneApmManager();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @throws BindingResolutionException
     *
     * @return void
     */
    public function boot(): void
    {
        /** @var Dispatcher $dispatcher */
        $dispatcher = $this->app->make(Dispatcher::class);

        $dispatcher->listen(RequestTerminated::class, DefaultTerminatedHandler::class);
        $dispatcher->listen(RequestHandled::class, RequestHandledHandler::class);
        $dispatcher->listen(RequestReceived::class, RequestReceivedHandler::class);
        $dispatcher->listen(WorkerStarting::class, RequestWorkerStartHandler::class);
        $dispatcher->listen(TaskReceived::class, TaskReceivedHandler::class);
        $dispatcher->listen(TaskTerminated::class, DefaultTerminatedHandler::class);
        $dispatcher->listen(TickReceived::class, TickReceivedHandler::class);
        $dispatcher->listen(TickTerminated::class, DefaultTerminatedHandler::class);
    }
}
