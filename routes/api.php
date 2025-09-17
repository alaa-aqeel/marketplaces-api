<?php

use App\Http\Controllers\API\v1\Order\OrderController;
use App\Http\Controllers\API\v1\Order\GetOrderController;
use App\Http\Controllers\API\v1\Product\GetProductByUrlController;
use App\Http\Controllers\API\v1\Product\GetProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware("log-latency")
    ->group(function() {

        Route::get("products", GetProductController::class);
        Route::get("product", GetProductByUrlController::class);


        Route::apiResource("orders", OrderController::class)->only(["index", "show"]);
        Route::middleware(["idempotency"])
            ->group(function() {

                Route::apiResource("orders", OrderController::class)->only(["store"]);
            });
    });
