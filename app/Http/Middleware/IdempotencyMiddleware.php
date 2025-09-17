<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class IdempotencyMiddleware
{
    public function handle($request, Closure $next)
    {
        $key = $request->header('Idempotency-Key');
        if (!$key) {
            return response()->json([
                'error' => __('Idempotency key is required')
            ], 400);
        }
        $dataHash = md5(json_encode($request->all()));
        $cacheKey = "idempotency:$key";

        if (Cache::has($cacheKey)) {
            $record = Cache::get($cacheKey);

            if ($record['data_hash'] === $dataHash) {
                return response()->json($record['response']);
            }

            return response()->json([
                'error' => __('Idempotency key already used with different data')
            ], 409);
        }

        $response = $next($request);

        Cache::put($cacheKey, [
            'data_hash' => $dataHash,
            'response'  => $response->getData(true),
        ], now()->addMinutes(60));

        return $response;
    }
}
