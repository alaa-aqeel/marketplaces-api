<?php

namespace App\Interfaces;

interface MarketplaceInterface {
    public function fetchProducts(array $paramaters = []): array;
    public function fetchProductDetails(string $productId): array;
}

