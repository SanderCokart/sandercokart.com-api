<?php

namespace App\Http\Middleware;

use Carbon\CarbonInterval;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class ThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        if (config('app.bypass_throttle')) {
            return $next($request);
        }

        if (is_string($maxAttempts)
            && func_num_args() === 3
            && ! is_null($limiter = $this->limiter->limiter($maxAttempts))) {
            return $this->handleRequestUsingNamedLimiter($request, $next, $maxAttempts, $limiter);
        }

        return $this->handleRequest(
            $request,
            $next,
            [
                (object)[
                    'key'              => $prefix . $this->resolveRequestSignature($request),
                    'maxAttempts'      => $this->resolveMaxAttempts($request, $maxAttempts),
                    'decayMinutes'     => $decayMinutes,
                    'responseCallback' => null,
                ],
            ]
        );
    }

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

        return is_callable($responseCallback)
            ? new HttpResponseException($responseCallback($request, $headers))
            : new ThrottleRequestsException(__('throttle.too-many-attempts', ['retry_after' => $retryAfter]), null, $headers);
    }
}
