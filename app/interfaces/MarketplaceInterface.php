<?php

namespace App\Interfaces;

use Illuminate\Http\Client\Pool;

interface MarketplaceInterface {
    public function withPool($pool);
    public function mapperList($data);
    public function mapper($data);
    public static function extractProductId(string $url);
    public function fetchProducts(array $paramaters = []);
    public function fetchProductDetails(string $productId);
}

