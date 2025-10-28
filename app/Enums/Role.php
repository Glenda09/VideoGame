<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'super_admin';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super administrador',
            self::Customer => 'Cliente',
        };
    }

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }
}

