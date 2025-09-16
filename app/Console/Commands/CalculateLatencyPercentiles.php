<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateLatencyPercentiles extends Command
{
    protected $signature = 'latency:percentiles';
    protected $description = 'Calculate p50, p95, p99 latency for each HTTP method + endpoint';

    public function handle()
    {
        // جلب كل المجموعات الفريدة من Method + Path
        $endpoints = DB::table('request_latencies')
            ->select('method', 'path')
            ->distinct()
            ->get();

        $rows = [];

        foreach ($endpoints as $endpoint) {
            $latencies = DB::table('request_latencies')
                ->where('method', $endpoint->method)
                ->where('path', $endpoint->path)
                ->orderBy('latency_ms')
                ->pluck('latency_ms')
                ->toArray();

            $total = count($latencies);
            if ($total === 0) continue;

            // حساب percentiles
            $p50 = $latencies[intval($total * 0.5)];
            $p95 = $latencies[intval($total * 0.95)];
            $p99 = $latencies[intval($total * 0.99)];

            $rows[] = [
                'Method' => $endpoint->method,
                'Path' => $endpoint->path,
                'p50 (ms)' => $p50,
                'p95 (ms)' => $p95,
                'p99 (ms)' => $p99,
            ];
        }

        if (empty($rows)) {
            $this->info('No latency data found.');
        } else {
            $this->table(
                ['Method', 'Path', 'p50 (ms)', 'p95 (ms)', 'p99 (ms)'],
                $rows
            );
        }

        return Command::SUCCESS;
    }
}
