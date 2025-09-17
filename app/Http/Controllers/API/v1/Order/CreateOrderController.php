<?php

namespace App\Http\Controllers\API\v1\Order;

use App\Enum\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class CreateOrderController extends Controller
{
    // Idempotency-Key
    public function __invoke(CreateOrderRequest $request)
    {
        $validated = $request->validated();

        $products = collect($validated["products"]);
        $validated['total_price'] = $products->sum("price");
        $validated['products_details'] = json_encode($validated["products"]);
        $validated["status"] = OrderStatus::Pending->value;
        $order = Order::create($validated);

        return $order;
    }
}
