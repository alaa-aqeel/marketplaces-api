<?php

namespace App\Marketplaces;

use App\Interfaces\MarketplaceInterface;

class AlibabaMarketplace extends BaseMarketplace implements MarketplaceInterface
{
    private function parseQuery(array $paramaters): array
    {
        return [
            "limit" => $paramaters["limit"] ?? 10,
            "select" => $paramaters["fields"] ?? '',
            "q" => $paramaters["search"] ?? ''
        ];
    }

    public function mapper($product) {

        return [
            'external_id' => $product['id'] ?? null,
            'source' => 'alibaba',
            'title' => $product['title'] ?? null,
            'description' => $product['description'] ?? null,
            'price' => $product['price'] ?? null,
            'currency' => $product['currency'] ?? "usd",
            'image' => $product['images'][0] ?? null,
            "image_hash" => $this->hashImage($product['images'][0] ?? null)
        ];
    }


    public function fetchProducts(array $paramaters = []): array
    {
        return $this->fetch(
            path: "products/search",
            parmaters: $this->parseQuery($paramaters),
            jsonPath: "products"
        );
    }

    public function fetchProductDetails(string $productId): array {
        return $this->fetch("products/$productId");
    }
}

