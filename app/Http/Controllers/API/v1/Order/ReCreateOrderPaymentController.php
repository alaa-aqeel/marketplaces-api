<?php

namespace App\Http\Controllers\API\v1\Order;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class ReCreateOrderPaymentController extends Controller
{
    function __construct(private OrderService $orderService)
    {}

    public function __invoke(Request $request, Order $order)
    {
        if ($order->status != OrderStatus::PaymentFailed) {
            return response()->json([
                "message" => "Can't create payment for this order",
                "status" => 'error'
            ], 400);
        }

        $payment = $this->orderService->createOrderPayment($order);

        return response()->json([
            "payment_id" => $payment->payment_id,
            "order_id" => $order->id,
            "order_status" => $order->status->name
        ]);
    }
}
