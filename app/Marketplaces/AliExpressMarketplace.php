<?php

namespace App\Marketplaces;

use App\Interfaces\MarketplaceInterface;
use Illuminate\Http\Client\Pool;

class AliExpressMarketplace extends BaseMarketplace implements MarketplaceInterface
{
    private function parseQuery(array $paramaters): array
    {
        return [
            "skip" => $paramaters["page"] ?? 10,
            "limit" => $paramaters["limit"] ?? 10,
            "select" => $paramaters["fields"] ?? '',
            "q" => $paramaters["search"] ?? ''
        ];
    }

    public static function extractProductId(string $url)
    {
        preg_match('/item\/(\d+)\.html/', $url, $matches);

        return $matches[1] ?? null;
    }

    public function mapper($data) {
        return [
            'external_id' => $data['id'] ?? null,
            'source' => 'aliexpress',
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? "usd",
            'image' => $data['images'][0] ?? null,
            // "image_hash" => $this->hashImage($data['images'][0] ?? null)
        ];
    }

    public function mapperList($data)
    {
        return $this->mapperResponse($data, "products");
    }

    public function fetchProducts(array $paramaters = [])
    {
        return $this->fetch(
            path: "products/search",
            parmaters: $this->parseQuery($paramaters),
        );
    }

    public function fetchProductDetails(string $productId)
    {
        $response = $this->fetch("products/$productId");

        return $this->mapperResponse($response);
    }
}
