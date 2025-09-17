<?php

namespace App\Http\Controllers\API\v1\Order;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    function __construct(private OrderRepository $orderRepository)
    {}

    public function store(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        $validated['products_details'] = json_encode($validated["products"]);
        $order = $this->orderRepository->create($validated);

        return new OrderResource($order);
    }

    public function show(Order $order)
    {
        return new OrderResource($order);
    }
}
