<?php

namespace App\Http\Middleware;

use Carbon\CarbonInterval;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Artisan;

class ThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    protected function buildException($request, $key, $maxAttempts, $responseCallback = null): HttpResponseException|ThrottleRequestsException
    {
        //time in seconds
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        //use carbon to format the time to minutes under 60 minutes or hours over 60 minutes
        $retryAfter = CarbonInterval::seconds($retryAfter)
            ->ceilMinute()
            ->forHumans([
                'parts' => 1,
            ]);

        if (config('app.bypass_throttle')) {
            Artisan::call('cache:clear');
        }

        return is_callable($responseCallback)
            ? new HttpResponseException($responseCallback($request, $headers))
            : new ThrottleRequestsException(__('throttle.too-many-attempts', ['retry_after' => $retryAfter]), null, $headers);
    }
}
