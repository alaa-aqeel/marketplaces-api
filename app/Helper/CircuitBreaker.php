<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cache;

class CircuitBreaker {
    private static $circuits = []; // نخزن حالات متعددة

    public static function call(string $serviceName, string $cacheKey, \Closure $callback) {
        $timeout  = config('services.circuit_breaker.timeout', 30);
        $maxFails = config('services.circuit_breaker.failures', 3);

        if (!isset(self::$circuits[$serviceName])) {
            self::$circuits[$serviceName] = [
                'failures' => 0,
                'state' => 'CLOSED',
                'lastTimeCall' => null,
            ];
        }

        $circuit = &self::$circuits[$serviceName];

        // Open → رفض فوري إلا بعد timeout
        if ($circuit['state'] === 'OPEN') {
            if (time() - $circuit['lastTimeCall'] > $timeout) {
                $circuit['state'] = 'HALF_OPEN';
            } else {
                return Cache::get($cacheKey, ['error' => "$serviceName unavailable"]);
            }
        }

        try {
            $result = $callback();
            Cache::put($cacheKey, $result, 3600);

            $circuit['failures'] = 0;
            $circuit['state'] = 'CLOSED';
            return $result;
        } catch (\Exception $e) {
            $circuit['failures']++;
            $circuit['lastTimeCall'] = time();

            if ($circuit['failures'] >= $maxFails) {
                $circuit['state'] = 'OPEN';
            }

            return Cache::get($cacheKey, ['error' => "$serviceName API failed"]);
        }
    }
}
