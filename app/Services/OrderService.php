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


    public function handleStatus(Order $order, OrderStatus $status)
    {
        if (!$order->status->canTransaction($status)) {
            abortError("Cannot change status from ".$order->status->name." to ".$status->name);
        }

        switch ($status) {
            case OrderStatus::Processing:
                return $this->handleProcessingStatus($order);
            case OrderStatus::Shipped:
                return $this->handleShippedStatus($order);
            case OrderStatus::Delivered:
                return $this->handleDeliveredStatus($order);
            case OrderStatus::Done:
                return $this->handleDoneStatus($order);
            case OrderStatus::Cancelled:
                return $this->handleCancelledStatus($order);
        }

        abortError("Failed handle status");
    }


    public function handleProcessingStatus(Order $order)
    {
        $order->update([
            "status" => OrderStatus::Processing->value
        ]);
        $order->refresh();

        return $order;
    }

    public function handleShippedStatus(Order $order)
    {
        return DB::transaction(function() use($order) {
            $order->update([
                "status" => OrderStatus::Shipped->value
            ]);
            $order->payments()
                ->where("status", PaymentStatus::Authorized->value)
                ->update(["status" => PaymentStatus::Paid->value])
                ;
            $order->refresh();

            return $order;
        });

    }

    public function handleDeliveredStatus(Order $order)
    {
        $order->update([
            "status" => OrderStatus::Delivered->value
        ]);
        $order->refresh();

        return $order;
    }

    public function handleDoneStatus(Order $order)
    {
        $order->update([
            "status" => OrderStatus::Done->value
        ]);
        $order->refresh();

        return $order;
    }

    public function handleCancelledStatus(Order $order)
    {
        $order->update([
            "status" => OrderStatus::Cancelled->value
        ]);
        $order->refresh();
        $order->payments()
            ->where("status", PaymentStatus::Paid->value)
            ->where("status", PaymentStatus::Authorized->value)
            ->update(["status" => PaymentStatus::Refunded->value])
            ;
        $order->refresh();

        return $order;
    }
}
