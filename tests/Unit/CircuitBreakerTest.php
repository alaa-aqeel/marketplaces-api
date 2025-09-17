<?php

namespace Tests\Unit;

use App\Helper\CircuitBreaker;
use App\Marketplaces\AlibabaMarketplace;
use App\Marketplaces\AliExpressMarketplace;
use App\Services\MarketplaceService;
use Exception;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CircuitBreakerTest extends TestCase
{

    function circuitBreaker($name, $cacheKey)
    {
        return new CircuitBreaker($name, $cacheKey);
    }

    function test_success_request()
    {
        $resp = $this->circuitBreaker("name", "cache_key")->call(function() {
            return Http::acceptJson()->get("https://dummyjson.com/products/search");
        }, resetTimeout: 60);
        $this->assertTrue($resp->successful());
    }

    function test_with_exception()
    {
        $resp = $this->circuitBreaker("name", "cache_key")->call(function() {
            return Http::acceptJson()->get("https://dummyjson.com/products/search");
        }, resetTimeout: 60);
        $this->assertTrue($resp->successful());

        $resp = $this->circuitBreaker("name", "cache_key")->call(function() { // get from cache
            throw new Exception("");
        }, resetTimeout: 60);
        $this->assertTrue($resp->successful());
    }

}
