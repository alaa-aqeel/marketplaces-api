<?php

use App\Helper\CircuitBreaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

if (!function_exists('normalizeArabic')) {
    /**
     * Normalize Arabic text for searching or processing.
     *
     * @param string $text
     * @return string
     */
    function normalizeArabic(string $text): string {

        $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $text = str_replace($arabic, $english, $text);

        $text = str_replace(['أ','إ','آ'], 'ا', $text);
        $text = str_replace('ى', 'ي', $text);
        $text = preg_replace('/[ًٌَُِْ]/u', '', $text); // حذف التشكيل
        $text = str_replace('ـ', '', $text); // حذف التطويل
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }
}

if (!function_exists('latency')) {
    function logLatency(string $name, \Closure $closure) {
        $start = microtime(true);
        $results = $closure();
        $durationMs = (int)((microtime(true) - $start) * 1000);
        $reflection = new ReflectionFunction($closure);
        Log::warning(strtolower("latency:".$name.":".$durationMs), [
            "function" => $reflection->getName(),
            "file" => $reflection->getFileName(),
            "latency" => $durationMs,
        ]);

        return $results;
    }
}

if (!function_exists("circuitBreaker")) {
    function circuitBreaker(string $name, $cacheKey, \Closure $closure) {
        return (new CircuitBreaker($name, $cacheKey))
            ->call($closure, config("services.circuit_breaker.timeout", 80));
    }
}


if (!function_exists("abort_error"))
{
    function abortError($message, int $statusCode = 400) {
        abort(response()->json([
            "status" => "error",
            "message" => $message,
        ], $statusCode));
    }
}
