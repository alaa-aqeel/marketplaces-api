<?php

namespace App\Models;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "fullname",
        "phone_number",
        "products_details",
        "total_price",
        "status",
        "status_details",
    ];

    public function getProductsDetailsAttribute($value)
    {
        return is_null($value) ? null : json_decode($value);
    }

    public function getStatusAttribute($value)
    {
        return OrderStatus::from($value);
    }

    public function getStatusDetailsAttribute($value)
    {
        return is_null($value) ? [] : json_decode($value);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, "order_id")
            ->whereIn("status", [
                PaymentStatus::Pending->value,
                PaymentStatus::Authorized->value,
                PaymentStatus::Paid->value,
            ]);
    }
}
