<?php

namespace App\Enum;



enum OrderStatus: int
{
    case Pending = 1;
    case Confirmed = 2;
    case Payment = 3;
    case Paid = 4;
    case Shipped = 5;
    case Delivered = 6;
    case Done = 7;
    case Failed = 8;
    case Cancelled = 9;
    case Rejected = 19;

    /**
     * Returns allowed next states from current state
     */
    public function statusMachine(): array
    {
        return match($this) {
            self::Pending => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Payment, self::Cancelled],
            self::Payment => [self::Paid, self::Failed],
            self::Paid => [self::Shipped, self::Failed],
            self::Shipped => [self::Delivered, self::Failed],
            self::Delivered => [self::Done, self::Rejected],
            self::Done => [], // final state
            self::Failed => [], // final state
            self::Cancelled => [], // final state
            self::Rejected => [], // final state
        };
    }
}
