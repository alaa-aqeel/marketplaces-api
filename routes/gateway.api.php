<?php

use App\Http\Controllers\Gateway\PaymentCheckoutController;
use Illuminate\Support\Facades\Route;




Route::post("gateway/payment-checkout", PaymentCheckoutController::class)->name("payment.checkout");
