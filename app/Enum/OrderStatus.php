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

    /**
     * Return possible next statuses from current status
     */
    public function statusMachine(): array
    {
        return match($this) {
            self::Pending => [],
            self::Confirmed => [
                self::Processing,
                self::Cancelled
            ],
            self::Processing => [
                self::Shipped,
                self::Cancelled
            ],
            self::Shipped => [
                self::Delivered,
                self::Cancelled
            ],
            self::Delivered => [
                self::Done
            ],
            self::Done => [],
            self::Cancelled => [],
        };
    }

    /**
     * Check if we can change to a new status
     */
    public function canTransaction(OrderStatus $status): bool
    {
        return in_array($status, $this->statusMachine());
    }
}

