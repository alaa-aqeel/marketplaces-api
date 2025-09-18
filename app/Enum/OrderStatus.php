<?php

namespace App\Enum;


enum OrderStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped   = 'shipped';
    case Delivered = 'delivered';
    case Done      = 'done';
    case Cancelled = 'cancelled';
}
