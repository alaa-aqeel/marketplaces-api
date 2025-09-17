<?php


namespace App\Services;

use App\Interfaces\MarketplaceInterface;
use App\Models\Product;
use Exception;

class MarketplaceService {


    private function getMarketplaces(): array
    {
        return config("marketplace");
    }

    public function getMarketplaceFromUrl(string $url): MarketplaceInterface|null
    {
        if (preg_match('#https?://(?:[a-z0-9-]+\.)*([a-z0-9-]+)\.[a-z]{2,}#i', $url, $matches)) {
            $source = strtolower($matches[1]);
            $commonTlds = ['com', 'org', 'net', 'edu', 'gov', 'co', 'io'];
            if (in_array($source, $commonTlds)) {
                if (preg_match('#https?://(?:[a-z0-9-]+\.)*([a-z0-9-]+)\.(?:[a-z0-9-]+\.)*([a-z]{2,})#i', $url, $deepMatches)) {
                    $source = strtolower($deepMatches[1]);
                }
            }

            if (is_null($source)) {
                return null;
            }
            $marketplace = config("marketplace.$source");
            if (is_null($marketplace)) {
                return null;
            }
            return $this->instanceMarketplace(config("marketplace.$source"));
        }
        return null;
    }

    public function extractProductId(string $url): null|string
    {
        return $this->getMarketplaceFromUrl($url)->extractProductId($url);
    }

    public function instanceMarketplace(array $marketplaceConfig): MarketplaceInterface
    {
        // Ensure 'class' key exists
        if (!isset($marketplaceConfig['class'])) {
            throw new \Exception("Marketplace class not defined in configuration.");
        }
        $marketplace = $marketplaceConfig['class'];
        if (!is_a($marketplace, MarketplaceInterface::class, true)) {
            throw new \Exception("Class must implement MarketplaceInterface");
        }
        $config = $marketplaceConfig['config'] ?? [];

        return new $marketplace($config);
    }

    /**
     * Update Or Insert Products
     */
    public function fetchAllProducts(string $search = "", int $limit = 10)
    {
        $products = [];
        foreach($this->getMarketplaces() as $name => $config) {
            $marketplace = $this->instanceMarketplace($config);
            $paramatersString = $search.":".$limit;
            $resp = circuitBreaker($name,
                "$name:$paramatersString",
                fn()=> $marketplace->mapperList($marketplace->fetchProducts([
                    "search" => $search,
                    "limit" => $limit
                ]))
            );
            $products = array_merge($products, $resp);
        }

        return $products;
    }

    /**
     * Fetch product by id
     *
     * @param \App\Interfaces\MarketplaceInterface $marketplace name of marketplace
     * @param mixed $id [string|int]
     * @return array|null
     * @throw Exception("invalid marketplace)
     */
    public function fetechProductById(MarketplaceInterface $marketplace, mixed $id)
    {
        $name = class_basename($marketplace::class); //
        return circuitBreaker($name,
            "$name:product-details:$id",
            function() use($marketplace, $id) {
                return $marketplace->fetchProductDetails($id);
            }
        );

    }

    public function getProductFromUrl(string $url)
    {
        $marketplace = $this->getMarketplaceFromUrl($url);
        if (is_null($marketplace)) {
            abort(response()->json([
                "message" => "Invalid Url",
            ]));
        }
        $id = $marketplace->extractProductId($url);
        $product = Product::where("external_id", $id)->first(); // first search in database
        if (is_null($product)) { // is null search in sites
            return $this->fetechProductById($marketplace, $id);
        }

        return $product;
    }
}
