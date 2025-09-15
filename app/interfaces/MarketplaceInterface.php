<?php

namespace App\Interfaces;

interface MarketplaceInterface {
    public static function extractProductId(string $url);
    public function fetchProducts(array $paramaters = []): array;
    public function fetchProductDetails(string $productId): array;
}

