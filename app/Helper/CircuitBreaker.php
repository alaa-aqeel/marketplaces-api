<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreaker
{
    private int $MaxFailures = 3;
    private array $circuit = [
        'failures' => 0,
        'state' => 'CLOSED',
        'lastFailureTime' => null,
    ];

    function __construct(
        private string $name,
        private string $cacheResultKey
    ) {
        $this->circuit = Cache::get("circuit:{$name}", [
            'failures' => 0,
            'state' => 'CLOSED',
            'lastFailureTime' => null,
        ]);
    }

    private function logStatus($state)
    {
        Log::warning("circuitbreaker-$this->name: $state");
    }

    private function onOpenStatus(float $resetTimeout)
    {
        if (time() - $this->circuit['lastFailureTime'] > $resetTimeout) {
            $this->circuit["state"] = "HALF_OPEN";
            Cache::put("circuit:{$this->name}", $this->circuit);
            $this->logStatus('OPEN (blocked)');
            return ;
        } else  {
            $this->logStatus('HALF OPEN try agine');
            return Cache::get($this->cacheResultKey, ['error' => "{$this->name} unavailable - Circuit OPEN"]);
        }
    }

    private function onFail()
    {
        $this->circuit['failures']++;
        $this->circuit['lastFailureTime'] = time();
        $this->logStatus("CLOSED (failure {$this->circuit['failures']}/{$this->MaxFailures})");
        if ($this->circuit['failures'] >= $this->MaxFailures) {
            $this->circuit['state'] = 'OPEN';
        }
        Cache::put("circuit:{$this->name}", $this->circuit);
    }

    public function call(\Closure $closure, float $resetTimeout)
    {
        if ($this->circuit['state'] === 'OPEN') {
            $result = $this->onOpenStatus($resetTimeout);
            if ($result) {
                return $result;
            }
        }
        try {
            $result = logLatency($this->cacheResultKey, function() use($closure) {
                return $closure();
            });
            Cache::put($this->cacheResultKey, $result, 3600);
            Cache::forget("circuit:{$this->name}");
            $this->logStatus('CLOSED (success)');
            return $result;
        } catch (\Exception $e) {
            $this->onFail();

            Cache::forget("ERROR: ".$e->getMessage());
            return Cache::get($this->cacheResultKey, [
                'error' => "{$this->name} API failed",
                'exception' => $e->getMessage()
            ]);
        }

    }

}
