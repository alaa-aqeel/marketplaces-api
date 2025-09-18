<?php

namespace App\Http\Resources\Order;

use App\Enum\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "fullname" => $this->fullname,
            "phone_number" => $this->phone_number,
            "total_price" => $this->total_price,
            "products" => $this->products_details,
            "status" => [
                "name" => $this->status->name,
                "value" => $this->status?->value
            ],
            "status_details" => $this->status_details,
            "payments" => $this->whenLoaded("payments"),
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at,
        ];
    }
}
