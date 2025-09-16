<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "payment_id",
        "method",
        "status",
        "details",
        "status",
        "amount",
        "order_id",
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, "order_id");
    }
}
