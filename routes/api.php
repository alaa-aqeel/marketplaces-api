<?php

use App\Http\Controllers\API\v1\Order\CreateOrderController;
use App\Http\Controllers\API\v1\Product\GetProductByUrlController;
use App\Http\Controllers\API\v1\Product\GetProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware("log-latency")
    ->group(function() {

        Route::get("products", GetProductController::class);
        Route::get("product", GetProductByUrlController::class);


        Route::middleware("idempotency")
            ->group(function() {

                Route::post("order", CreateOrderController::class)->name("order.store");
            });
    });
