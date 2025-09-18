<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case Pending    = 'pending';
    case Authorized = 'authorized';
    case Paid       = 'paid';
    case Voided     = 'voided';
    case Refunded   = 'refunded';
    case Failed     = 'failed';
}
