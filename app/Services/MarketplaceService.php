<?php


namespace App\Services;

use App\Helper\CircuitBreaker;
use App\Interfaces\MarketplaceInterface;
use App\Repositories\ProductRepository;
use Exception;
use Mockery\Expectation;

class MarketplaceService {


    private function getMarketplaces(): array
    {
        return config("marketplace");
    }

    private function instanceMarketplace(array $marketplaceConfig): MarketplaceInterface
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

    public function fetchMarketplaceProducts($name, $marketplaceConfig, array $paramaters = [])
    {
        $paramatersString = $paramaters['search'].":".$paramaters['limit'];
        return CircuitBreaker::call($name, "$name:$paramatersString", function() use($marketplaceConfig, $paramaters) {

            return $this->instanceMarketplace($marketplaceConfig)->fetchProducts($paramaters);
        });
    }

    /**
     * Update Or Insert Products
     */
    public function fetchAllProducts(string $search = "", int $limit = 10)
    {
        $products = [];
        foreach($this->getMarketplaces() as $name => $marketplace) {
            $fetchProducts = $this->fetchMarketplaceProducts($name, $marketplace, [
                "limit" => $limit,
                "search" => $search
            ]);
            $products = array_merge($products, $fetchProducts);
        }

        return $products;
    }

    /**
     * Fetch product by id
     *
     * @param string $marketplace name of marketplace
     * @param mixed $id [string|int]
     * @return array|null
     * @throw Exception("invalid marketplace)
     */
    public function fetechProductById(string $marketplace, mixed $id)
    {
        if (is_null($id)) {
            return null;
        }
        if (in_array($marketplace, array_keys(config("marketplace")))) {
            throw new Exception(__("Select invalid marketplace"));
        }

        return CircuitBreaker::call($marketplace, "$marketplace:product-details:$id", function() use($marketplace, $id) {
            return $this
                    ->instanceMarketplace(config("marketplace.$marketplace"))
                    ->fetchProductDetails($id);
        });

    }

}
