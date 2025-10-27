<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'PENDING';
    case RequiresPayment = 'REQUIRES_PAYMENT';
    case Paid = 'PAID';
    case Canceled = 'CANCELED';
    case Failed = 'FAILED';
    case Refunded = 'REFUNDED';

    public function isFinal(): bool
    {
        return in_array($this, [self::Paid, self::Canceled, self::Failed, self::Refunded], true);
    }

    public function canRetryPayment(): bool
    {
        return in_array($this, [self::Pending, self::RequiresPayment, self::Failed], true);
    }
}
