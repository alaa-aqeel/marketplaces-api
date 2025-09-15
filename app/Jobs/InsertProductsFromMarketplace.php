<?php

namespace App\Jobs;

use App\Interfaces\MarketplaceInterface;
use App\Repositories\ProductRepository;
use App\Services\MarketplaceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InsertProductsFromMarketplace implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $source,
        private int $limit = 500
    ) {
        $this->source = strtolower($this->source);
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $marketplace = config("marketplace.{$this->source}");
        $service = new MarketplaceService;
        $repository = new ProductRepository;
        $products = $service->instanceMarketplace($marketplace)->fetchProducts(["limit" => $this->limit]);
        $repository->upsert($products);

    }
}
