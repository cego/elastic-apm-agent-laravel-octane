<?php

namespace App\Octane\Listeners;

use Elastic\Apm\ElasticApm;
use Elastic\Apm\Impl\GlobalTracerHolder;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Events\RequestTerminated;

class ElasticApmRequestTerminatedHandler
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     *
     * @return void
     */
    public function handle(object $event): void
    {
        Log::info(__METHOD__ . ' ' . class_basename($event));
        $tx = $event->sandbox['ElasticApmTransaction'];
        Log::info(__METHOD__ . ' sandbox[ElasticApmTransaction] is: ' . class_basename($tx));
        $tx->end();
    }
}
