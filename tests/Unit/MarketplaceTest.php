<?php

namespace Tests\Unit;

use App\Marketplaces\AlibabaMarketplace;
use App\Marketplaces\AliExpressMarketplace;
use App\Services\MarketplaceService;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    // private function assertProductStructure(array $product): void
    // {
    //     $this->assertArrayHasKey('external_id', $product);
    //     $this->assertArrayHasKey('source', $product);
    //     $this->assertArrayHasKey('title', $product);
    //     $this->assertArrayHasKey('description', $product);
    //     $this->assertArrayHasKey('price', $product);
    //     $this->assertArrayHasKey('currency', $product);
    //     $this->assertArrayHasKey('image', $product);
    // }

    // public function test_fetch_products_from_aliexpress_marketplace(): void
    // {
    //     $marketplaces = new AliExpressMarketplace(config("marketplace.aliexpress.config"));
    //     $products = $marketplaces->fetchProducts();

    //     $this->assertIsArray($products);
    //     $this->assertNotEmpty($products);
    //     $this->assertProductStructure($products[0]);
    // }

    // public function test_fetch_product_details_by_id_from_aliexpress_marketplace(): void
    // {
    //     $marketplaces = new AliExpressMarketplace(config("marketplace.aliexpress.config"));
    //     $products = $marketplaces->fetchProducts();
    //     $this->assertIsArray($products);
    //     $this->assertNotEmpty($products);

    //     $productId = $products[0]['external_id'];

    //     $productDetails = $marketplaces->fetchProductDetails($productId);
    //     $this->assertIsArray($productDetails);
    //     $this->assertProductStructure($productDetails);
    // }


    // public function test_fetch_product_details_from_alibaba_marketplace(): void
    // {
    //     $marketplaces = new AlibabaMarketplace(config("marketplace.alibaba.config"));
    //     $products = $marketplaces->fetchProducts();

    //     $this->assertIsArray($products);
    //     $this->assertNotEmpty($products);
    //     $this->assertProductStructure($products[0]);
    // }

    // public function test_fetch_product_details_by_id_from_alibaba_marketplace(): void
    // {
    //     $marketplaces = new AlibabaMarketplace(config("marketplace.alibaba.config"));
    //     $products = $marketplaces->fetchProducts();
    //     $this->assertIsArray($products);
    //     $this->assertNotEmpty($products);

    //     $productId = $products[0]['external_id'];

    //     $productDetails = $marketplaces->fetchProductDetails($productId);
    //     $this->assertIsArray($productDetails);
    //     $this->assertProductStructure($productDetails);
    // }


    // public function test_get_all_products()
    // {
    //     $service = new MarketplaceService();
    //     $products = $service->fetchAllProducts(limit: 5);

    //     $this->assertEquals(5 * 2, count($products), count($products));
    // }

    // public function test_search_products()
    // {
    //     $service = new MarketplaceService();
    //     $products = $service->fetchAllProducts(search: "aa");

    //     $this->assertNotEmpty($products);
    // }

    public function test_extract_product_id_from_id()
    {
        $service = new MarketplaceService();
        $urls = [
            "https://www.aliexpress.com/item/1005006170828360.html",
            "https://aliexpress.com/item/1005006170828360.html",
            "https://www.alibaba.com/product-detail/1600795066971.html",
            "https://ar.alibaba.com/product-detail/22471019.html",
        ];
        foreach($urls as $url) {
            $source = $service->getMarketplaceFromUrl($url);
            $this->assertNotNull($source, $url);

            $id = $source->extractProductId($url);
            $this->assertNotNull($id, $url);
        }
    }
}
