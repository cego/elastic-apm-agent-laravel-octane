<?php

namespace App\Octane\Listeners;

use Elastic\Apm\ElasticApm;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Events\RequestHandled;
use Laravel\Octane\Events\RequestReceived;

class ElasticApmRequestHandledHandler
{
    /**
     * Handle the event.
     *
     * @param  RequestHandled  $event
     *
     * @return void
     */
    public function handle(RequestHandled $event): void
    {
        $event->sandbox['ElasticApmRequestSpan']->end();
    }
}
