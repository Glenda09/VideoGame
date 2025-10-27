<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'PENDING';
    case Approved = 'APPROVED';
    case Declined = 'DECLINED';
    case Voided = 'VOIDED';

    public function isSuccessful(): bool
    {
        return $this === self::Approved;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Approved, self::Declined, self::Voided], true);
    }
}
