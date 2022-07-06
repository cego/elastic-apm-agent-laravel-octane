<?php

namespace App\Octane\Listeners;

use Elastic\Apm\ElasticApm;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Events\RequestReceived;

class ElasticApmRequestReceivedHandler
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
        $txName = $event->request->method() . ' ' . $event->request->path();

        Log::info(__METHOD__ . ' ' . class_basename($event) . ': ' . $txName);

        if (! ElasticApm::getCurrentTransaction()->hasEnded()) {
            ElasticApm::getCurrentTransaction()->discard();
        }

        $event->sandbox['ElasticApmTransaction'] = ElasticApm::beginCurrentTransaction(
            $txName,
            'request'
        );

        $span = ElasticApm::getCurrentTransaction()->beginChildSpan(
            'child_span_name',
            'child_span_type'
        );
        $event->sandbox['ElasticApmRequestSpan'] = $span;
    }
}
