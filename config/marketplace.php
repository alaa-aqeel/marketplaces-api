<?php

return [
    "aliexpress" => [
        "class" => \App\Marketplaces\AliExpressMarketplace::class,
        "config" => [
            "name" => "aliexpress",
            "api_url" => env('ALIEXPRESS_API_URL', 'https://dummyjson.com'),
            "app_key" => env('ALIEXPRESS_APP_KEY', ''),
            "app_secret" => env('ALIEXPRESS_APP_SECRET', ''),
        ],
    ],
    "alibaba" => [
        "class" => \App\Marketplaces\AlibabaMarketplace::class,
        "config" => [
            "name" => "alibaba",
            "api_url" => env('ALIBABA_API_URL', 'https://dummyjson.com'),
            "app_key" => env('ALIBABA_APP_KEY', ''),
            "app_secret" => env('ALIBABA_APP_SECRET', ''),
        ],
    ],
];
