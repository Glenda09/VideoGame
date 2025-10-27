<?php

namespace App\Enums;

enum CouponType: string
{
    case Fixed = 'fixed';
    case Percent = 'percent';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Descuento fijo',
            self::Percent => 'Descuento porcentual',
        };
    }

    public function appliesPercentage(): bool
    {
        return $this === self::Percent;
    }
}
