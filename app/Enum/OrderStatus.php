<?php

namespace App\Enum;



enum OrderStatus: int
{
    case Pending = 1;
    case Confirmed = 2;
    case CreatePayment = 3;
    case PaidPayment = 4;
    case Delivery = 4;
    case Done = 5;
    case Failed = 6;
    case Cancelled = 7;

    /**
     * Returns allowed next states from current state
     */
    public function statusMachine(): array
    {
        return match($this) {
            self::Pending => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::CreatePayment, self::Cancelled],
            self::CreatePayment => [self::PaidPayment, self::Failed],
            self::PaidPayment => [self::Delivery, self::Failed],
            self::Delivery => [self::Done, self::Failed],
            self::Done => [], // final state
            self::Failed => [], // final state
            self::Cancelled => [], // final state
        };
    }
}
