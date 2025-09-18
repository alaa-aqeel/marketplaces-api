<?php


namespace App\Services;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\OrderRepository;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    function __construct(private OrderRepository $orderRepository)
    {}

    public function createOrder(array $data): array
    {
        return DB::transaction(function () use($data) {
            if (!isset($data["products"])) {
                throw new Exception("products is required");
            }
            $data['products_details'] = json_encode($data["products"]);
            $data['stauts'] = OrderStatus::Pending->value;
            $order = $this->orderRepository->create($data);
            $payment = Payment::create([
                "payment_id" => Str::uuid(),
                "gateway" => "test",
                "status" => PaymentStatus::Pending->value,
                "amount" => $order->total_price,
                "currency" => "usd",
                "order_id" => $order->id,
            ]);

            return [
                "payment_id" => $payment->payment_id,
                "order_id" => $order->id,
            ];
        });

    }
}
