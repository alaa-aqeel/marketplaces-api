<?php

namespace App\Marketplaces;

use App\Interfaces\MarketplaceInterface;
use Exception;
use Illuminate\Support\Str;

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

    public static function extractProductId(string $url)
    {
        preg_match('/detail\/(\d+)\.html/', $url, $matches);
        return $matches[1] ?? null;
    }

    public function mapper($product) {
        if (!isset($product['id'])) {
            throw new Exception("product id missing");
        }
        return [
            "id" => Str::uuid(),
            'source_external_id' => "alibaba:".$product['id'],
            'external_id' => $product['id'],
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

