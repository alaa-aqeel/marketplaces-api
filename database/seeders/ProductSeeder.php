<?php

namespace Database\Seeders;

use App\Repositories\ProductRepository;
use App\Services\MarketplaceService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    private function importDataFromSites($source, $limit = 100)
    {
        $marketplace = config("marketplace.{$source}");
        $service = new MarketplaceService;
        $repository = new ProductRepository;
        $products = $service->instanceMarketplace($marketplace)->fetchProducts(["limit" => $limit]);
        $repository->upsert($products);

    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::factory(5)->create([
            "source" => "aliexpress"
        ]);
        \App\Models\Product::factory(5)->create([
            "source" => "alibaba"
        ]);

        // foreach(config("marketplace") as $key => $marketplace ){
        //     $this->importDataFromSites($key , 10);
        // }
    }
}
