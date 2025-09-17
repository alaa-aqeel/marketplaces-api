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

    /**
     * Extract Source name from url
     *
     * @param string $url
     * @return null|\App\Interfaces\MarketplaceInterface
     */
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
            $marketplaceConfig = config("marketplace.$source");
            if (is_null($marketplaceConfig)) {
                return null;
            }
            return $this->instanceMarketplace($marketplaceConfig);
        }
        return null;
    }

    /**
     * Extract id name from url
     *
     * @param string $url
     * @return null|string|int
     */
    public function extractProductId(string $url): null|string|int
    {
        return $this->getMarketplaceFromUrl($url)->extractProductId($url);
    }

    /**
     * Get instance of class marketplace
     *
     * @param array $config
     * @return \App\Interfaces\MarketplaceInterface
     */
    public function instanceMarketplace(array $config): MarketplaceInterface
    {
        // Ensure 'class' key exists
        if (!isset($config['class'])) {
            throw new \Exception("Marketplace class not defined in configuration.");
        }
        $marketplace = $config['class'];
        if (!is_a($marketplace, MarketplaceInterface::class, true)) {
            throw new \Exception("Class must implement MarketplaceInterface");
        }

        return new $marketplace($config);
    }

    /**
     * Fetch products for marketplace
     *
     * @param string $name
     * @param array $args = []
     * @return array
     */
    public function fetchProductsFor(string $name, array $args = [])
    {
        $marketplace = $this->instanceMarketplace(config("marketplace.$name"));
        return circuitBreaker($name, "marketplace:$name:".join(":", $args),
            fn()=> $marketplace->mapperList($marketplace->fetchProducts($args))
        );
    }

    /**
     * Fetch All product from all sources
     *
     * @param string $serch
     * @param int $page
     * @param int limit
     * @return array
     */
    public function fetchAllProducts(string $search = "", int $page = 1, int $limit = 10)
    {
        $products = [];
        foreach($this->getMarketplaces() as $name => $config) {
            $resp = $this->fetchProductsFor($name, [
                "search" => $search,
                "page" => $page,
                "limit" => $limit,
            ]);
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
            "marketplace:$name:product-details:$id",
            function() use($marketplace, $id) {
                return $marketplace->fetchProductDetails($id);
            }
        );

    }

    /**
     * Fetch product from url
     *
     * @param string $url
     * @return array|null
     * @abort("invalid url", 400)
     */
    public function getProductFromUrl(string $url)
    {
        $marketplace = $this->getMarketplaceFromUrl($url);
        if (is_null($marketplace)) {
            abort(response()->json([
                "message" => "Invalid Url",
            ]), 400);
        }
        $id = $marketplace->extractProductId($url);
        $product = Product::where("external_id", $id)->first(); // first search in database
        if (is_null($product)) { // is null search in sites
            return $this->fetechProductById($marketplace, $id);
        }

        return $product;
    }
}
