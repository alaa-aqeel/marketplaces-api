<?php

namespace App\Http\Controllers\API\v1\Order;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class StatusOrderController extends Controller
{

    function __construct(private OrderService $orderService)
    {}

    public function __invoke(Request $request, Order $order)
    {
        $request->validate([
            "status" => ["required", new Enum(OrderStatus::class)]
        ]);

        $status = OrderStatus::from($request->get("status"));
        $this->orderService->handleStatus($order, $status);

        return response()->json([
            "status" => "success",
            "message" => "Order status has been successful change to ".$status->name
        ]);
    }
}
