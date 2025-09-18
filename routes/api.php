<?php

use App\Http\Controllers\API\v1\Order\CreateOrderController;
use App\Http\Controllers\API\v1\Order\OrderController;
use App\Http\Controllers\API\v1\Order\ReCreateOrderPaymentController;
use App\Http\Controllers\API\v1\Order\StatusOrderController;
use App\Http\Controllers\API\v1\Product\GetProductByUrlController;
use App\Http\Controllers\API\v1\Product\GetProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware("log-latency")
    ->group(function() {

        Route::get("products", GetProductController::class);
        Route::get("product", GetProductByUrlController::class);


        Route::apiResource("orders", OrderController::class)->only(["show"]);
        Route::middleware(["idempotency"])
            ->group(function() {

                Route::post("orders", CreateOrderController::class);
                Route::post("orders/{order}/payment", ReCreateOrderPaymentController::class);
            });

        Route::post("orders/{order}/status", StatusOrderController::class);
    });



include "gateway.api.php";
