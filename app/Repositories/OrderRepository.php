<?php

namespace App\Repositories;

use App\Enum\OrderStatus;
use App\Helper\FileUpload;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    /**
     * Filter orders based on provided criteria.
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(array $filters = [])
    {
        $query = Product::query();
        $query->when($filters['id'] ?? null, function ($q, $source) {
            $q->where('id', $source);
        });
        $query->when($filters['phone_number'] ?? null, function ($q, $source) {
            $q->where('phone_number', $source);
        });
        $query->when($filters['status'] ?? null, function ($q, $source) {
            $q->where('status', $source);
        });

        return $query;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use($data) {
            $products = collect($data["products"]);
            $data['total_price'] = $products->sum("price");
            $data["status"] = OrderStatus::Pending->value;

            return Order::create($data);
        });

    }

    public function get($id)
    {
        return Order::whereId($id)->first();
    }
}
