<?php

namespace App\Http\Controllers\Gateway;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentCheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            "payment_id" => "required|string|uuid",
        ]);
        $payment = Payment::where("payment_id", $request->get("payment_id"))->first();
        if (is_null($payment)) {
            return response()->json("not found", 404);
        }
        $payment->update(["status" => PaymentStatus::Authorized->value]);
        $payment->order()->update(["status" => OrderStatus::Confirmed->value]);

        return response()->json("successful authrized payment", 200);
    }
}
