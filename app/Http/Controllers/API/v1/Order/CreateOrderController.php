<?php

namespace App\Http\Controllers\API\v1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CreateOrderController extends Controller
{
    function __construct(private OrderService $orderService)
    {}

    public function __invoke(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        $order = $this->orderService->createOrder($validated);

        return response()->json($order);
    }
}
