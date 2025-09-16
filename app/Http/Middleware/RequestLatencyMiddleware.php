<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestLatencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $durationMs = (microtime(true) - $start) * 1000;
        DB::table('request_latencies')->insert([
            "id" => Str::uuid(),
            'method' => $request->method(),
            'path' => $request->path(),
            'latency_ms' => (int)$durationMs,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $response;
    }
}
