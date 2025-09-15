<?php

namespace App\Jobs;

use App\Repositories\ProductRepository;
use App\Services\MarketplaceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchMarketplaceProducts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new MarketplaceService;
        $repository = new ProductRepository;



    }
}
