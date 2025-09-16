<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreaker
{
    private static function logCircuitBreaker($name, $state)
    {
        Log::warning("circuitbreaker-$name: $state");
    }

    public static function call(string $serviceName, string $cacheKey, \Closure $callback)
    {
        $maxFails = config('services.circuit_breaker.failures', 3);
        $resetTimeout = config('services.circuit_breaker.reset_timeout', 60);

        $circuit = Cache::get("circuit:{$serviceName}", [
            'failures' => 0,
            'state' => 'CLOSED',
            'lastFailureTime' => null,
        ]);

        if ($circuit['state'] === 'OPEN') {
            // Check if reset timeout has passed
            if (time() - $circuit['lastFailureTime'] > $resetTimeout) {
                $circuit['state'] = 'HALF_OPEN';
                Cache::put("circuit:{$serviceName}", $circuit, $resetTimeout);
            } else {
                static::logCircuitBreaker($serviceName, 'OPEN (blocked)');
                return Cache::get($cacheKey, ['error' => "{$serviceName} unavailable - Circuit OPEN"]);
            }
        }

        try {
            $result = logLatency($cacheKey, function() use($callback) {
                return $callback();
            });
            Cache::put($cacheKey, $result, 3600);
            $circuit['failures'] = 0;
            $circuit['state'] = 'CLOSED';
            $circuit['lastFailureTime'] = null;
            Cache::put("circuit:{$serviceName}", $circuit, $resetTimeout);

            static::logCircuitBreaker($serviceName, 'CLOSED (success)');
            return $result;
        } catch (\Exception $e) {
            // Increment failure count
            $circuit['failures']++;
            $circuit['lastFailureTime'] = time();

            // Open circuit if max failures reached
            if ($circuit['failures'] >= $maxFails) {
                $circuit['state'] = 'OPEN';
                static::logCircuitBreaker($serviceName, 'OPEN (tripped)');
            } else {
                static::logCircuitBreaker($serviceName, "CLOSED (failure {$circuit['failures']}/{$maxFails})");
            }

            // Update circuit state in Redis
            Cache::put("circuit:{$serviceName}", $circuit, $resetTimeout);

            // Return cached value if available
            return Cache::get($cacheKey, [
                'error' => "{$serviceName} API failed",
                'exception' => $e->getMessage()
            ]);
        }
    }
}
